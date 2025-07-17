<?php

namespace App\Models;

use CodeIgniter\Model;

class PengemasanModel extends Model
{
    protected $table = 'pengemasan';
    protected $primaryKey = 'id';
    protected $db;
    protected $stokModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->stokModel = new StokModel();
    }

    /**
     * Menyimpan data pengemasan dari form.
     */
    public function simpanPengemasan(array $data)
    {
        $this->db->transBegin();
        
        try {
            $item_diproses = false;
            foreach ($data['items'] as $item) {
                if (empty($item['jumlah']) || $item['jumlah'] <= 0 || empty($item['gudang']) || empty($item['mesin']) || empty($item['jenis_produksi'])) continue;
                
                $item_diproses = true;
                $nom_jenis_produksi = $item['jenis_produksi'];

                $data_produk_db = $this->db->table('produk p')
                    ->select('p.id_produk, p.satuan_per_dus')
                    ->join('tbl_jenis_produksi j', 'p.nama_produk = j.group_jenis_produksi')
                    ->where('j.nom_jenis_produksi', $nom_jenis_produksi)
                    ->get()->getRowArray();

                if (!$data_produk_db) throw new \Exception("Jenis produksi tidak memiliki produk hasil yang cocok.");

                $id_produk = $data_produk_db['id_produk'];
                $satuan_per_dus = (int)$data_produk_db['satuan_per_dus'];
                $perubahan_dus = ($satuan_per_dus > 1) ? (int)$item['jumlah'] : 0;
                $perubahan_satuan = ($satuan_per_dus <= 1) ? (int)$item['jumlah'] : 0;

                $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?", [$perubahan_dus, $perubahan_satuan, $id_produk, (int)$item['gudang']]);
                
                if ($this->db->affectedRows() === 0) {
                    $this->db->table('stok_produk')->insert(['id_produk' => $id_produk, 'id_gudang' => (int)$item['gudang'], 'jumlah_dus' => $perubahan_dus, 'jumlah_satuan' => $perubahan_satuan]);
                }

                $this->db->table('pengemasan')->insert(['tanggal' => $data['tTanggal'], 'shift' => $data['tShift'], 'gudang_id' => (int)$item['gudang'], 'mesin' => $item['mesin'], 'produk_id' => $id_produk, 'jumlah_dus' => $perubahan_dus, 'jumlah_satuan' => $perubahan_satuan]);
            }
            
            if (!$item_diproses) throw new \Exception("Tidak ada item valid untuk disimpan.");

            if ($this->db->transStatus() === false) throw new \Exception('Gagal menyimpan ke database.');
            
            $this->db->transCommit();
            return ['status' => 'success', 'message' => 'Data pengemasan berhasil disimpan!'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }
    }

    /**
     * Mengambil riwayat pengemasan berdasarkan filter.
     */
   // app/Models/PengemasanModel.php
    public function getRiwayat(array $filters)
    {
        $builder = $this->db->table('pengemasan pg')
            ->select('pg.id, pg.tanggal, pg.shift, pg.mesin, pr.nama_produk, g.nama_gudang, pg.jumlah_dus, pg.jumlah_satuan')
            ->join('produk pr', 'pg.produk_id = pr.id_produk')
            ->join('gudang g', 'pg.gudang_id = g.id_gudang')
            ->where('DATE(pg.tanggal) >=', $filters['tgl_mulai'])
            ->where('DATE(pg.tanggal) <=', $filters['tgl_akhir']);

        if ($filters['gudang_id'] !== 'semua') $builder->where('pg.gudang_id', $filters['gudang_id']);
        if ($filters['produk_id'] !== 'semua') $builder->where('pg.produk_id', $filters['produk_id']);

        return $builder->orderBy('pg.tanggal', 'DESC')->orderBy('pg.id', 'DESC')->get()->getResultArray();
    }

    /**
     * Mengambil detail riwayat untuk modal edit.
     */
    public function getDetailRiwayat(int $id)
    {
        $data = $this->db->table('pengemasan pg')
            ->select('pg.id, pg.tanggal, pg.produk_id, pg.gudang_id, pg.jumlah_dus, pg.jumlah_satuan, p.nama_produk, g.nama_gudang')
            ->join('produk p', 'pg.produk_id = p.id_produk')
            ->join('gudang g', 'pg.gudang_id = g.id_gudang')
            ->where('pg.id', $id)
            ->get()->getRowArray();

        if ($data) {
            $stock_historis = $this->stokModel->getHistoricalStock($data['produk_id'], $data['gudang_id'], date('Y-m-d', strtotime($data['tanggal'])));
            $data['stok_gudang_historis_dus'] = $stock_historis['dus'];
            $data['stok_gudang_historis_satuan'] = $stock_historis['satuan'];
        }
        return $data;
    }

    /**
     * Memperbarui data pengemasan dan stoknya.
     */
    public function updatePengemasan(array $data)
    {
        $id = (int)$data['id'];
        $new_dus = (int)$data['jumlah_dus'];
        $new_satuan = (int)$data['jumlah_satuan'];

        $this->db->transBegin();

        $old_data = $this->db->table('pengemasan')->where('id', $id)->get()->getRowArray();
        if (!$old_data) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Data pengemasan tidak ditemukan.'];
        }
        
        $selisih_dus = $new_dus - $old_data['jumlah_dus'];
        $selisih_satuan = $new_satuan - $old_data['jumlah_satuan'];
        
        $stok_saat_ini = $this->db->table('stok_produk')->where('id_produk', $old_data['produk_id'])->where('id_gudang', $old_data['gudang_id'])->get()->getRowArray();
            
        if (($stok_saat_ini['jumlah_dus'] + $selisih_dus) < 0 || ($stok_saat_ini['jumlah_satuan'] + $selisih_satuan) < 0) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Stok tidak akan mencukupi di masa depan jika perubahan ini disimpan.'];
        }

        $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?", [$selisih_dus, $selisih_satuan, $old_data['produk_id'], $old_data['gudang_id']]);
        $this->db->table('pengemasan')->where('id', $id)->update(['jumlah_dus' => $new_dus, 'jumlah_satuan' => $new_satuan]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Gagal memperbarui data.'];
        }

        $this->db->transCommit();
        return ['status' => 'success', 'message' => 'Riwayat pengemasan berhasil diperbarui!'];
    }

    /**
     * Menghapus data pengemasan dan mengurangi stok.
     */
    public function hapusPengemasan(int $id)
    {
        $this->db->transBegin();
        
        $data_to_delete = $this->db->table('pengemasan')->where('id', $id)->get()->getRowArray();
        if (!$data_to_delete) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Data pengemasan tidak ditemukan.'];
        }

        $stok_saat_ini = $this->db->table('stok_produk')->where('id_produk', $data_to_delete['produk_id'])->where('id_gudang', $data_to_delete['gudang_id'])->get()->getRowArray();

        if (!$stok_saat_ini || ($stok_saat_ini['jumlah_dus'] < $data_to_delete['jumlah_dus']) || ($stok_saat_ini['jumlah_satuan'] < $data_to_delete['jumlah_satuan'])) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Gagal menghapus. Stok di gudang saat ini tidak mencukupi untuk dikurangi.'];
        }

        $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus - ?, jumlah_satuan = jumlah_satuan - ? WHERE id_produk = ? AND id_gudang = ?", [$data_to_delete['jumlah_dus'], $data_to_delete['jumlah_satuan'], $data_to_delete['produk_id'], $data_to_delete['gudang_id']]);
        $this->db->table('pengemasan')->where('id', $id)->delete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Gagal menghapus data.'];
        }

        $this->db->transCommit();
        return ['status' => 'success', 'message' => 'Riwayat pengemasan berhasil dihapus! Stok telah dikurangi.'];
    }
}