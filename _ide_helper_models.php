<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $kategori_id
 * @property string $judul
 * @property string $deskripsi
 * @property string|null $gambar
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KategoriArtikel $kategori
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereGambar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereKategoriId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artikel whereUserId($value)
 */
	class Artikel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tiket_id
 * @property string $judul
 * @property string $kategori
 * @property string $konten
 * @property string|null $gambar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tiket $tiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi whereGambar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi whereKonten($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi whereTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketReqPublikasi whereUpdatedAt($value)
 */
	class DetailTiketReqPublikasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tiket_id
 * @property string $aplikasi
 * @property string $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tiket $tiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun whereAplikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun whereTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketResetAkun whereUpdatedAt($value)
 */
	class DetailTiketResetAkun extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tiket_id
 * @property string $keperluan
 * @property string $tahun_ajaran
 * @property int $semester
 * @property string|null $keperluan_lainnya
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tiket $tiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif whereKeperluan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif whereKeperluanLainnya($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif whereTahunAjaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif whereTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketSuratKetAktif whereUpdatedAt($value)
 */
	class DetailTiketSuratKetAktif extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tiket_id
 * @property string $data_nama_lengkap
 * @property string $data_tmp_lahir
 * @property string $data_tgl_lhr
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tiket $tiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs whereDataNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs whereDataTglLhr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs whereDataTmpLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs whereTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTiketUbahDataMhs whereUpdatedAt($value)
 */
	class DetailTiketUbahDataMhs extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $layanan_id
 * @property string $judul
 * @property string $deskripsi
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Layanan $layanan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereLayananId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereUserId($value)
 */
	class Faq extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_jabatan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereNamaJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereUpdatedAt($value)
 */
	class Jabatan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $kategori
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriArtikel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriArtikel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriArtikel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriArtikel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriArtikel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriArtikel whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriArtikel whereUpdatedAt($value)
 */
	class KategoriArtikel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tiket_id
 * @property int $pengirim_id
 * @property string $komentar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $pengirim
 * @property-read \App\Models\Tiket $tiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket whereKomentar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket wherePengirimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket whereTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KomentarTiket whereUpdatedAt($value)
 */
	class KomentarTiket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama
 * @property int $status_arsip
 * @property int $unit_id
 * @property int $prioritas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Staff> $penanggungJawab
 * @property-read int|null $penanggung_jawab_count
 * @property-read \App\Models\Unit $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan wherePrioritas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereStatusArsip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Layanan whereUpdatedAt($value)
 */
	class Layanan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $program_studi_id
 * @property string|null $nim
 * @property string|null $tahun_masuk
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProgramStudi $programStudi
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa whereNim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa whereProgramStudiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa whereTahunMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mahasiswa whereUserId($value)
 */
	class Mahasiswa extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $program_studi
 * @property string $jurusan_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramStudi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramStudi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramStudi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramStudi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramStudi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramStudi whereJurusanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramStudi whereProgramStudi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramStudi whereUpdatedAt($value)
 */
	class ProgramStudi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tiket_id
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tiket $tiket
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket whereTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatusTiket whereUserId($value)
 */
	class RiwayatStatusTiket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $unit_id
 * @property int $jabatan_id
 * @property string $nik
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Jabatan $jabatan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Layanan> $layanan
 * @property-read int|null $layanan_count
 * @property-read \App\Models\Unit $unit
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereJabatanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Staff whereUserId($value)
 */
	class Staff extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $no_tiket
 * @property int $pemohon_id
 * @property int $layanan_id
 * @property string $deskripsi
 * @property int|null $jawaban_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KomentarTiket|null $jawaban
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KomentarTiket> $komentar
 * @property-read int|null $komentar_count
 * @property-read \App\Models\Layanan $layanan
 * @property-read \App\Models\User $pemohon
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereJawabanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereLayananId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereNoTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket wherePemohonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereUpdatedAt($value)
 */
	class Tiket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_unit
 * @property int|null $kepala_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Staff|null $kepalaUnit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereKepalaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereNamaUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUpdatedAt($value)
 */
	class Unit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $email_personal
 * @property string|null $no_wa
 * @property string|null $google_id
 * @property string|null $avatar
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artikel> $artikel
 * @property-read int|null $artikel_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KomentarTiket> $komentarTiket
 * @property-read int|null $komentar_tiket_count
 * @property-read \App\Models\Mahasiswa|null $mahasiswa
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Staff|null $staff
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tiket> $tiket
 * @property-read int|null $tiket_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailPersonal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoWa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

