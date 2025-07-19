<?php

namespace App\Models;

use CodeIgniter\Model;

class OperpackKemasUlangModel extends Model
{
    protected $table = 'operpack_kemas_ulang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['produk_id', 'tanggal', 'jumlah_kemas'];
    protected $useTimestamps = false;
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Mengambil data stok untuk kemas ulang
     */
    public function getStokRepack(int $produk_id): array
    {
        if ($produk_id <= 0) {
            return [
                'hasil_seleksi_aman' => 0,
                'hasil_kemas_ulang' => 0,
                'stok_aman_siap_repack_pcs' => 0,
                'satuan_per_dus' => 1,
                'max_unit' => 0,
                'sisa_pcs' => 0,
                'unit_type' => 'satuan',
                'unit_label' => 'Satuan/Pcs',
                'nama_produk' => ''
            ];
        }

        $result = $this->db->table('view_stok_overpack vso')
            ->select('vso.hasil_seleksi_aman, vso.hasil_kemas_ulang, (vso.hasil_seleksi_aman - vso.hasil_kemas_ulang) as stok_aman_siap_repack_pcs, p.satuan_per_dus, p.nama_produk')
            ->join('produk p', 'vso.id_produk = p.id_produk')
            ->where('vso.id_produk', $produk_id)
            ->get()->getRowArray();

        if ($result) {
            $satuanPerDus = (int)$result['satuan_per_dus'];
            $stokPcs = (int)$result['stok_aman_siap_repack_pcs'];
            $stokPcs = $stokPcs < 0 ? 0 : $stokPcs;

            // Tentukan unit input berdasarkan satuan_per_dus
            if ($satuanPerDus > 1) {
                // Produk dalam dus - input dalam dus
                $maxUnit = floor($stokPcs / $satuanPerDus);
                $sisaPcs = $stokPcs % $satuanPerDus;
                $unitType = 'dus';
                $unitLabel = 'Dus';
            } else {
                // Produk satuan - input dalam satuan/pcs
                $maxUnit = $stokPcs;
                $sisaPcs = 0;
                $unitType = 'satuan';
                $unitLabel = 'Satuan/Pcs';
            }

            return [
                'hasil_seleksi_aman' => (int)$result['hasil_seleksi_aman'],
                'hasil_kemas_ulang' => (int)$result['hasil_kemas_ulang'],
                'stok_aman_siap_repack_pcs' => $stokPcs,
                'satuan_per_dus' => $satuanPerDus,
                'max_unit' => $maxUnit,
                'sisa_pcs' => $sisaPcs,
                'unit_type' => $unitType,
                'unit_label' => $unitLabel,
                'nama_produk' => $result['nama_produk']
            ];
        }

        return [
            'hasil_seleksi_aman' => 0,
            'hasil_kemas_ulang' => 0,
            'stok_aman_siap_repack_pcs' => 0,
            'satuan_per_dus' => 1,
            'max_unit' => 0,
            'sisa_pcs' => 0,
            'unit_type' => 'satuan',
            'unit_label' => 'Satuan/Pcs',
            'nama_produk' => ''
        ];
    }

    /**
     * Mengambil data riwayat kemas ulang untuk ditampilkan di tabel
     */
    public function getRiwayat(array $filters): array
    {
        $builder = $this->db->table('operpack_kemas_ulang k')
            ->select('k.id, k.tanggal, pr.nama_produk, k.jumlah_kemas')
            ->join('produk pr', 'k.produk_id = pr.id_produk')
            ->where('k.tanggal >=', $filters['tanggal_mulai'])
            ->where('k.tanggal <=', $filters['tanggal_akhir']);

        if ($filters['produk_id'] !== 'semua') {
            $builder->where('k.produk_id', (int)$filters['produk_id']);
        }

        return $builder->orderBy('k.tanggal', 'DESC')->orderBy('k.id', 'DESC')->get()->getResultArray();
    }

    /**
     * Mengambil detail satu item kemas ulang untuk diedit
     */
    public function getDetailWithStok(int $id): ?array
    {
        $data = $this->db->table('operpack_kemas_ulang k')
            ->select('k.id, k.produk_id, k.tanggal, k.jumlah_kemas, p.nama_produk, p.satuan_per_dus')
            ->join('produk p', 'k.produk_id = p.id_produk')
            ->where('k.id', $id)
            ->get()->getRowArray();

        if ($data) {
            // Hitung stok siap kemas
            $stokOverpack = $this->db->table('view_stok_overpack')
                ->select('(hasil_seleksi_aman - hasil_kemas_ulang) as stok_siap_kemas')
                ->where('id_produk', $data['produk_id'])
                ->get()->getRowArray();

            $data['stok_siap_kemas'] = $stokOverpack ? (int)$stokOverpack['stok_siap_kemas'] : 0;
            $data['stok_siap_kemas_plus_current'] = $data['stok_siap_kemas'] + $data['jumlah_kemas'];
            $data['satuan_per_dus'] = $data['satuan_per_dus'] ? (int)$data['satuan_per_dus'] : 1;
        }

        return $data;
    }

    /**
     * Memperbarui data kemas ulang yang sudah ada
     */
    public function updateKemasUlang(int $id, int $newKemas): array
    {
        $this->db->transBegin();

        try {
            $oldData = $this->find($id);
            if (!$oldData) {
                throw new \Exception("Data lama tidak ditemukan.");
            }

            // Get product info
            $produkInfo = $this->db->table('produk p')
                ->select('p.satuan_per_dus, g.id_gudang')
                ->join('gudang g', "g.nama_gudang = 'Overpack'", 'cross')
                ->where('p.id_produk', $oldData['produk_id'])
                ->get()->getRowArray();

            if (!$produkInfo) {
                throw new \Exception("Data produk/gudang Overpack tidak ditemukan.");
            }

            $satuanPerDus = $produkInfo['satuan_per_dus'] > 0 ? (int)$produkInfo['satuan_per_dus'] : 1;
            $idGudangOverpack = $produkInfo['id_gudang'];

            // Validasi kelipatan untuk dus
            if ($satuanPerDus > 1 && ($newKemas % $satuanPerDus != 0)) {
                throw new \Exception("Jumlah kemas ($newKemas) harus kelipatan dari satuan per dus ($satuanPerDus).");
            }

            // Cek stok tersedia
            $stokOverpack = $this->db->table('view_stok_overpack')
                ->select('(hasil_seleksi_aman - hasil_kemas_ulang) as stok_siap_kemas')
                ->where('id_produk', $oldData['produk_id'])
                ->get()->getRowArray();

            $stokAvailablePlusCurrent = ($stokOverpack ? (int)$stokOverpack['stok_siap_kemas'] : 0) + $oldData['jumlah_kemas'];

            if ($newKemas > $stokAvailablePlusCurrent) {
                throw new \Exception("Jumlah kemas ($newKemas) melebihi stok tersedia ($stokAvailablePlusCurrent pcs).");
            }

            // Hitung selisih dan update stok
            $selisihPcs = $newKemas - $oldData['jumlah_kemas'];
            $perubahanDus = floor($selisihPcs / $satuanPerDus);
            $perubahanSatuan = $selisihPcs % $satuanPerDus;

            // Update stok produk
            $this->db->table('stok_produk')
                ->where('id_produk', $oldData['produk_id'])
                ->where('id_gudang', $idGudangOverpack)
                ->set('jumlah_dus', 'jumlah_dus + ' . $perubahanDus, false)
                ->set('jumlah_satuan', 'jumlah_satuan + ' . $perubahanSatuan, false)
                ->update();

            // Update data kemas ulang
            $this->update($id, ['jumlah_kemas' => $newKemas]);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal memperbarui data.');
            }

            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat kemas ulang berhasil diperbarui!'];

        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Menghapus data kemas ulang
     */
    public function hapusKemasUlang(int $id): array
    {
        $this->db->transBegin();

        try {
            $dataToDelete = $this->find($id);
            if (!$dataToDelete) {
                throw new \Exception('Data untuk dihapus tidak ditemukan.');
            }

            // Get product info
            $produkInfo = $this->db->table('produk p')
                ->select('p.satuan_per_dus, g.id_gudang')
                ->join('gudang g', "g.nama_gudang = 'Overpack'", 'cross')
                ->where('p.id_produk', $dataToDelete['produk_id'])
                ->get()->getRowArray();

            if (!$produkInfo) {
                throw new \Exception("Data produk/gudang Overpack tidak ditemukan.");
            }

            $satuanPerDus = $produkInfo['satuan_per_dus'] > 0 ? (int)$produkInfo['satuan_per_dus'] : 1;
            $idGudangOverpack = $produkInfo['id_gudang'];

            $pcsToRemove = $dataToDelete['jumlah_kemas'];
            $dusToRemove = floor($pcsToRemove / $satuanPerDus);
            $satuanToRemove = $pcsToRemove % $satuanPerDus;

            // Cek stok sekarang
            $stokSekarang = $this->db->table('stok_produk')
                ->select('jumlah_dus, jumlah_satuan')
                ->where('id_produk', $dataToDelete['produk_id'])
                ->where('id_gudang', $idGudangOverpack)
                ->get()->getRowArray();

            if (!$stokSekarang || $stokSekarang['jumlah_dus'] < $dusToRemove || $stokSekarang['jumlah_satuan'] < $satuanToRemove) {
                throw new \Exception("Stok produk jadi di gudang Overpack tidak mencukupi untuk dibatalkan.");
            }

            // Update stok produk (kurangi)
            $this->db->table('stok_produk')
                ->where('id_produk', $dataToDelete['produk_id'])
                ->where('id_gudang', $idGudangOverpack)
                ->set('jumlah_dus', 'jumlah_dus - ' . $dusToRemove, false)
                ->set('jumlah_satuan', 'jumlah_satuan - ' . $satuanToRemove, false)
                ->update();

            // Delete data
            $this->delete($id);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menghapus data dari database.');
            }

            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat kemas ulang berhasil dihapus!'];

        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Menyimpan data kemas ulang baru dengan validasi stok
     */
    public function simpanRepack(array $data): array
    {
        $this->db->transBegin();

        try {
            $idProduk = (int)$data['id_produk'];
            $tanggal = $data['tanggal'];
            $jumlahKemasUnit = (int)$data['jumlah_kemas_unit'];

            if ($jumlahKemasUnit <= 0) {
                throw new \Exception("Jumlah kemas ulang harus lebih dari 0.");
            }

            // Validasi stok tersedia dan konversi
            $stokData = $this->getStokRepack($idProduk);
            $stokPcsTersedia = (int)$stokData['stok_aman_siap_repack_pcs'];
            $stokPcsTersedia = $stokPcsTersedia < 0 ? 0 : $stokPcsTersedia;
            $satuanPerDus = (int)$stokData['satuan_per_dus'];

            // Konversi input ke PCS berdasarkan satuan_per_dus
            if ($satuanPerDus > 1) {
                // Input dalam dus - konversi ke pcs
                $jumlahKemasPcs = $jumlahKemasUnit * $satuanPerDus;
                $maxUnitTersedia = floor($stokPcsTersedia / $satuanPerDus);
                $unitLabel = 'dus';

                // Validasi tidak melebihi dus yang tersedia
                if ($jumlahKemasUnit > $maxUnitTersedia) {
                    throw new \Exception("Jumlah yang dikemas ($jumlahKemasUnit dus) melebihi dus yang tersedia ($maxUnitTersedia dus).");
                }
            } else {
                // Input dalam satuan/pcs
                $jumlahKemasPcs = $jumlahKemasUnit;
                $maxUnitTersedia = $stokPcsTersedia;
                $unitLabel = 'satuan';

                // Validasi tidak melebihi satuan yang tersedia
                if ($jumlahKemasUnit > $maxUnitTersedia) {
                    throw new \Exception("Jumlah yang dikemas ($jumlahKemasUnit satuan) melebihi satuan yang tersedia ($maxUnitTersedia satuan).");
                }
            }

            // Simpan log ke tabel operpack_kemas_ulang (dalam PCS)
            $insertData = [
                'produk_id' => $idProduk,
                'tanggal' => $tanggal,
                'jumlah_kemas' => $jumlahKemasPcs
            ];

            $this->insert($insertData);

            // Tambahkan stok ke Gudang Overpack
            $gudangOverpack = $this->db->table('gudang')->where('nama_gudang', 'Overpack')->get()->getRowArray();
            if (!$gudangOverpack) {
                throw new \Exception("Gudang 'Overpack' tidak ditemukan di database.");
            }

            $idGudangOverpack = $gudangOverpack['id_gudang'];

            // Update stok berdasarkan satuan_per_dus
            if ($satuanPerDus == 1) {
                // Jika satuan_per_dus = 1, langsung ke jumlah_satuan
                $dusTambah = 0;
                $satuanTambah = $jumlahKemasPcs;
                $updateInfo = "Stok di Gudang Overpack telah ditambahkan: $satuanTambah satuan.";
            } else {
                // Jika satuan_per_dus > 1, konversi PCS ke Dus & Satuan
                $dusTambah = floor($jumlahKemasPcs / $satuanPerDus);
                $satuanTambah = $jumlahKemasPcs % $satuanPerDus;
                $updateInfo = "Stok di Gudang Overpack telah ditambahkan: $dusTambah dus + $satuanTambah satuan.";
            }

            // Update stok produk
            $this->db->table('stok_produk')
                ->where('id_produk', $idProduk)
                ->where('id_gudang', $idGudangOverpack)
                ->set('jumlah_dus', 'jumlah_dus + ' . $dusTambah, false)
                ->set('jumlah_satuan', 'jumlah_satuan + ' . $satuanTambah, false)
                ->update();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data ke database.");
            }

            $this->db->transCommit();

            $successMessage = "Data kemas ulang berhasil disimpan! ";
            $successMessage .= "Input: $jumlahKemasUnit $unitLabel = $jumlahKemasPcs pcs. ";
            $successMessage .= $updateInfo;

            return ['success' => true, 'message' => $successMessage];

        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

}
