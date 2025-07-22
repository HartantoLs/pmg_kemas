<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // Gudang Methods
    public function getGudangList()
    {
        $builder = $this->db->table('gudang');
        return $builder->orderBy('id_gudang', 'ASC')->get()->getResultArray();
    }

    public function insertGudang($data)
    {
        $builder = $this->db->table('gudang');
        return $builder->insert($data);
    }

    public function updateGudang($id, $data)
    {
        $builder = $this->db->table('gudang');
        return $builder->where('id_gudang', $id)->update($data);
    }

    public function deleteGudang($id)
    {
        $builder = $this->db->table('gudang');
        return $builder->where('id_gudang', $id)->delete();
    }

    // Produk Methods
    public function getProdukList()
    {
        $builder = $this->db->table('produk');
        return $builder->orderBy('id_produk', 'ASC')->get()->getResultArray();
    }

    public function insertProduk($data)
    {
        $builder = $this->db->table('produk');
        return $builder->insert($data);
    }

    public function updateProduk($id, $data)
    {
        $builder = $this->db->table('produk');
        return $builder->where('id_produk', $id)->update($data);
    }

    public function deleteProduk($id)
    {
        $builder = $this->db->table('produk');
        return $builder->where('id_produk', $id)->delete();
    }

    // Jenis Produksi Methods
    public function getJenisProduksiList()
    {
        $builder = $this->db->table('tbl_jenis_produksi');
        return $builder->orderBy('nom_jenis_produksi', 'ASC')->get()->getResultArray();
    }

    public function getJenisProduksiById($id)
    {
        $builder = $this->db->table('tbl_jenis_produksi');
        return $builder->where('nom_jenis_produksi', $id)->get()->getRowArray();
    }

    public function getBahanBakuByJenisProduksi($id)
    {
        $builder = $this->db->table('tbl_rinci_jenis_produksi r');
        $builder->select('r.*, b.nama_barang, b.satuan');
        $builder->join('tbl_barang b', 'r.kode_barang = b.kode_barang', 'left');
        $builder->where('r.nom_jenis_produksi', $id);
        return $builder->get()->getResultArray();
    }

    public function insertJenisProduksi($jenisProduksiData, $produkData, $bahanBaku = [])
    {
        $this->db->transStart();

        try {
            // Insert ke tbl_jenis_produksi
            $builder = $this->db->table('tbl_jenis_produksi');
            $builder->insert($jenisProduksiData);
            $insertId = $this->db->insertID();

            // Insert ke tbl_produk (jika belum ada produk dengan nama yang sama)
            $existingProduk = $this->db->table('produk')
                ->where('nama_produk', $produkData['nama_produk'])
                ->get()->getRowArray();
            
            if (!$existingProduk) {
                $this->db->table('produk')->insert($produkData);
            }

            // Insert bahan baku ke tbl_rinci_jenis_produksi
            if (!empty($bahanBaku)) {
                foreach ($bahanBaku as $bahan) {
                    $bahanData = [
                        'nom_jenis_produksi' => $insertId,
                        'kode_barang' => $bahan['kode_barang'],
                        'jumlah' => $bahan['jumlah']
                    ];
                    $this->db->table('tbl_rinci_jenis_produksi')->insert($bahanData);
                }
            }

            $this->db->transComplete();
            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function updateJenisProduksi($id, $jenisProduksiData, $bahanBaku = [])
    {
        $this->db->transStart();

        try {
            // Update tbl_jenis_produksi saja, tidak update produk
            $builder = $this->db->table('tbl_jenis_produksi');
            $builder->where('nom_jenis_produksi', $id)->update($jenisProduksiData);

            // Delete existing bahan baku
            $this->db->table('tbl_rinci_jenis_produksi')
                ->where('nom_jenis_produksi', $id)
                ->delete();

            // Insert new bahan baku
            if (!empty($bahanBaku)) {
                foreach ($bahanBaku as $bahan) {
                    $bahanData = [
                        'nom_jenis_produksi' => $id,
                        'kode_barang' => $bahan['kode_barang'],
                        'jumlah' => $bahan['jumlah']
                    ];
                    $this->db->table('tbl_rinci_jenis_produksi')->insert($bahanData);
                }
            }

            $this->db->transComplete();
            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function deleteJenisProduksi($id)
    {
        $this->db->transStart();

        try {
            if (!$id) {
                throw new \Exception('ID tidak dikirim atau kosong');
            }

            // Debug 1: Cek apakah ID tersebut ada di database
            $check = $this->db->table('tbl_jenis_produksi')->where('nom_jenis_produksi', $id)->get()->getRow();
            if (!$check) {
                throw new \Exception("Data dengan ID {$id} tidak ditemukan di tbl_jenis_produksi");
            }

            // Delete rinciannya dulu
            $rinciDeleteResult = $this->db->table('tbl_rinci_jenis_produksi')
                ->where('nom_jenis_produksi', $id)
                ->delete();

            // Debug 2: Cek hasil delete rinci
            if ($rinciDeleteResult === false) {
                throw new \Exception("Gagal delete rinci jenis produksi untuk ID {$id}");
            }

            // Delete jenis produksinya
            $jenisDeleteResult = $this->db->table('tbl_jenis_produksi')
                ->where('nom_jenis_produksi', $id)
                ->delete();

            // Debug 3: Cek hasil delete jenis produksi
            if ($jenisDeleteResult === false) {
                throw new \Exception("Gagal delete jenis produksi utama untuk ID {$id}");
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaksi gagal. Hasil deleteRinci: " . json_encode($rinciDeleteResult) . ", deleteJenis: " . json_encode($jenisDeleteResult));
            }

            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[DELETE_JENIS_PRODUKSI] ' . $e->getMessage());
            throw $e;
        }
    }

    // Barang Methods
    public function getBarangList()
    {
        $builder = $this->db->table('tbl_barang');
        $builder->select('kode_barang, nama_barang, satuan, konversi, utama');
        $builder->orderBy('nama_barang', 'ASC');
        return $builder->get()->getResultArray();
    }
}
