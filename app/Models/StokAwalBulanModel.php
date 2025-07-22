<?php

namespace App\Models;

use CodeIgniter\Model;

class StokAwalBulanModel extends Model
{
    protected $table = 'stok_awal_bulan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tanggal_opname', 'produk_id', 'gudang_id', 'jumlah_dus_opname', 'jumlah_satuan_opname'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDataByMonth($year, $month)
    {
        return $this->where('YEAR(tanggal_opname)', $year)
                   ->where('MONTH(tanggal_opname)', $month)
                   ->findAll();
    }

    public function getMutasiByPeriod($start_date, $end_date)
    {
        return $this->db->table('v_semua_transaksi')
                       ->select('produk_id, gudang_id, SUM(perubahan_dus) as total_mutasi_dus, SUM(perubahan_satuan) as total_mutasi_satuan')
                       ->where('tanggal_transaksi >=', $start_date)
                       ->where('tanggal_transaksi <=', $end_date)
                       ->groupBy('produk_id, gudang_id')
                       ->get()
                       ->getResultArray();
    }

    public function saveOpname($data)
    {
        $existing = $this->where([
            'tanggal_opname' => $data['tanggal_opname'],
            'produk_id' => $data['produk_id'],
            'gudang_id' => $data['gudang_id']
        ])->first();

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    public function recalculateFollowingMonths($tanggal_opname)
    {
        $recalculated_months_list = [];
        $recalc_start_date = new \DateTime($tanggal_opname);
        $recalc_start_date->modify('+1 month');

        // Tentukan batas akhir rekalkulasi
        $result_max_date = $this->selectMax('tanggal_opname')->first();
        $last_opname_in_db = $result_max_date['tanggal_opname'] ?? null;

        $end_date_db = $last_opname_in_db ? new \DateTime($last_opname_in_db) : new \DateTime('1970-01-01');
        $end_date_current = new \DateTime(date('Y-m-01'));
        $recalc_end_date = ($end_date_db > $end_date_current) ? $end_date_db : $end_date_current;

        while ($recalc_start_date <= $recalc_end_date) {
            $current_recalc_date = clone $recalc_start_date;

            // Ambil data lama
            $old_stock_data = [];
            $old_data = $this->getDataByMonth($current_recalc_date->format('Y'), $current_recalc_date->format('m'));
            foreach ($old_data as $row) {
                $old_stock_data[$row['produk_id']][$row['gudang_id']] = [
                    'dus' => (int)$row['jumlah_dus_opname'],
                    'satuan' => (int)$row['jumlah_satuan_opname']
                ];
            }

            // Hitung data baru
            $new_stock_data = [];
            $prev_month_date = (clone $current_recalc_date)->modify('-1 month');
            $prev_opname_data = $this->getDataByMonth($prev_month_date->format('Y'), $prev_month_date->format('m'));
            
            foreach ($prev_opname_data as $row) {
                $new_stock_data[$row['produk_id']][$row['gudang_id']] = [
                    'dus' => (int)$row['jumlah_dus_opname'],
                    'satuan' => (int)$row['jumlah_satuan_opname']
                ];
            }

            $mutasi_data = $this->getMutasiByPeriod($prev_month_date->format('Y-m-01'), $prev_month_date->format('Y-m-t'));
            foreach ($mutasi_data as $row) {
                if (!isset($new_stock_data[$row['produk_id']][$row['gudang_id']])) {
                    $new_stock_data[$row['produk_id']][$row['gudang_id']] = ['dus' => 0, 'satuan' => 0];
                }
                $new_stock_data[$row['produk_id']][$row['gudang_id']]['dus'] += (int)$row['total_mutasi_dus'];
                $new_stock_data[$row['produk_id']][$row['gudang_id']]['satuan'] += (int)$row['total_mutasi_satuan'];
            }

            // Bandingkan dan simpan jika ada perubahan
            $is_month_recalculated = false;
            foreach ($new_stock_data as $produk_id => $gudang_data) {
                foreach ($gudang_data as $gudang_id => $new_jumlah) {
                    $old_jumlah = $old_stock_data[$produk_id][$gudang_id] ?? ['dus' => 0, 'satuan' => 0];

                    if ($new_jumlah['dus'] != $old_jumlah['dus'] || $new_jumlah['satuan'] != $old_jumlah['satuan']) {
                        $data = [
                            'tanggal_opname' => $current_recalc_date->format('Y-m-d'),
                            'produk_id' => $produk_id,
                            'gudang_id' => $gudang_id,
                            'jumlah_dus_opname' => $new_jumlah['dus'],
                            'jumlah_satuan_opname' => $new_jumlah['satuan']
                        ];
                        $this->saveOpname($data);
                        $is_month_recalculated = true;
                    }
                }
            }

            if ($is_month_recalculated) {
                $recalculated_months_list[] = $current_recalc_date->format('F Y');
            }

            $recalc_start_date->modify('+1 month');
        }

        return $recalculated_months_list;
    }
}
