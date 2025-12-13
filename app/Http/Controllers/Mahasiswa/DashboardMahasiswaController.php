<?php
namespace App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardMahasiswaController extends Controller
{
  private $validStatuses = [
    'Diajukan_oleh_Pemohon',
    'Dinilai_Belum_Selesai_oleh_Pemohon',
    'Dinilai_Selesai_oleh_Pemohon',
  ];

  public function index()
  {
    $userId = Auth::id();
    $data_tiket = Tiket::with(['layanan.unit', 'pemohon', 'statusAkhir'])
      ->where('pemohon_id', $userId)
      ->orderBy('created_at', 'desc')
      ->get();

    $total_tiket = $data_tiket->count();  

    // Status yang dianggap selesai
    $statusSelesai = [
      'Dinilai_Selesai_oleh_Pemohon'
    ];

    // Hitung tiket yang status akhirnya termasuk status selesai
    $tiket_selesai = $data_tiket->filter(function ($tiket) use ($statusSelesai) {
      $statusTerbaru = $tiket->statusAkhir ? $tiket->statusAkhir->status : null;
      return $statusTerbaru && in_array($statusTerbaru, $statusSelesai);
    })->count();

    $belumSelesai = $total_tiket - $tiket_selesai;

    // Hitung persentase
    $persentase_selesai = $total_tiket > 0 ? round(($tiket_selesai / $total_tiket) * 100, 1) : 0;
    $persentase_belum_selesai = $total_tiket > 0 ? round(($belumSelesai / $total_tiket) * 100, 1) : 0;

    // Hitung rata-rata tiket per bulan (6 bulan terakhir)
    $sixMonthsAgo = Carbon::now()->subMonths(6);
    $tiket_6_bulan = Tiket::where('pemohon_id', $userId)
      ->where('created_at', '>=', $sixMonthsAgo)
      ->count();
    
    $rata_rata_per_bulan = round($tiket_6_bulan / 6, 1);

    // Data untuk chart mingguan (7 hari terakhir)
    $weekly_data = [];
    for ($i = 6; $i >= 0; $i--) {
      $date = Carbon::now()->subDays($i);
      $count = Tiket::where('pemohon_id', $userId)
        ->whereDate('created_at', $date->toDateString())
        ->count();
      $weekly_data[] = $count;
    }

    // Tiket berdasarkan prioritas
    $prioritas_tinggi = $data_tiket->where('prioritas', 'Tinggi')->count();
    $prioritas_sedang = $data_tiket->where('prioritas', 'Sedang')->count();
    $prioritas_rendah = $data_tiket->where('prioritas', 'Rendah')->count();

    // Response time rata-rata (dalam hari)
    $avg_response_time = $data_tiket->filter(function($tiket) {
      return $tiket->statusAkhir && $tiket->statusAkhir->status === 'Dinilai_Selesai_oleh_Pemohon';
    })->map(function($tiket) {
      return Carbon::parse($tiket->created_at)->diffInDays($tiket->statusAkhir->created_at);
    })->avg();

    $avg_response_time = $avg_response_time ? round($avg_response_time, 1) : 0;

    return view('content.apps.mahasiswa.dashboard-mahasiswa', compact(
      'data_tiket',
      'total_tiket',
      'tiket_selesai',
      'belumSelesai',
      'persentase_selesai',
      'persentase_belum_selesai',
      'rata_rata_per_bulan',
      'weekly_data',
      'prioritas_tinggi',
      'prioritas_sedang',
      'prioritas_rendah',
      'avg_response_time'
    ), ['pageConfigs' => $this->pageConfigs]);
  }
}