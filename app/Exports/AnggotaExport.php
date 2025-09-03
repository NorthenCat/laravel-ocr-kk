<?php

namespace App\Exports;

use App\Models\Anggota;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AnggotaExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, ShouldAutoSize, WithColumnFormatting
{
    protected $rwId;
    protected $rowNumber = 0;
    protected $includeFilename;

    public function __construct($rwId, $includeFilename = true)
    {
        $this->rwId = $rwId;
        $this->includeFilename = $includeFilename;
    }

    public function collection()
    {
        return Anggota::with(['getKk.getRw.getDesa'])
            ->whereHas('getKk', function ($query) {
                $query->where('rw_id', $this->rwId);
            })
            ->orderBy('no_kk', 'asc')
            ->get();
    }

    public function headings(): array
    {
        $headings = [
            'NO',
        ];

        if ($this->includeFilename) {
            $headings[] = 'Filename';
        }

        $headings = array_merge($headings, [
            'NIK',
            'NomorKK',
            'NamaLengkap',
            'JenisKelamin',
            'TempatLahir',
            'TanggalLahir',
            'Alamat',
            'JenisPekerjaan',
            'Pendapatan',
            'NoHP',
            'Agama',
            'GolonganDarah',
            'SUKU',
            'StatusHubunganDalamKeluarga',
            'StatusPerkawinan',
            'Ayah',
            'Ibu',
            'Pendidikan',
            'DTKS(Y/T)',
            'SERTIFIKAT LAHIR',
            'AKTA NIKAH',
            'ASURANSI KESEHATAN',
            'PENDUDUK TETAP (Y/T)',
            'DISABILITAS',
            'VAKSIN1 (Y/T)',
            'VAKSIN2 (Y/T)',
            'VAKSIN3 (Y/T)',
            'TGL DATANG',
            'TGL_PINDAH',
            'PINDAH KE',
            'TGL MENINGGAL',
            'WAKTU MENINGGAL',
            'PENDATANG (Y/T)',
            'YATIM/PIATU/YATIMPIATU',
            'DUSUN',
            'RT',
            'RW',
            'Desa/Kelurahan',
            'Kecamatan',
            'KabupatenKota',
            'Provinsi',
            'Kewarganegaraan',
            'sdgs1',
            'sdgs2',
            'sdgs3',
            'sdgs4',
            'sdgs5',
            'sdgs6',
            'sdgs7',
            'sdgs8',
            'sdgs9',
            'sdgs10',
            'sdgs11',
            'sdgs12',
            'sdgs13',
            'sdgs14',
            'sdgs15',
            'sdgs16',
            'sdgs17',
            'sdgs18',
            'sdgs19',
            'sdgs20',
            'sdgs21',
            'sdgs22',
            'sdgs23',
            'sdgs24',
            'sdgs25',
            'sdgs26',
            'sdgs27',
            'sdgs28',
            'sdgs29',
            'sdgs30',
            'sdgs31',
            'sdgs32'
        ]);

        return $headings;
    }

    public function map($anggota): array
    {
        $this->rowNumber++;

        $data = [
            $this->rowNumber,
        ];

        if ($this->includeFilename) {
            $data[] = $anggota->img_name ?? '';
        }

        $data = array_merge($data, [
            $anggota->nik ?? '',
            $anggota->no_kk ?? $anggota->getKk->no_kk ?? '',
            $anggota->nama_lengkap ?? '',
            $anggota->jenis_kelamin ?? '',
            $anggota->tempat_lahir ?? '',
            $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('d/m/Y') : '',
            $anggota->alamat ?? '',
            $anggota->jenis_pekerjaan ?? '',
            '', // Pendapatan - not in our model
            '', // NoHP - not in our model
            $anggota->agama ?? '',
            $anggota->golongan_darah ?? '',
            '', // SUKU - not in our model
            $anggota->status_hubungan_dalam_keluarga ?? '',
            $anggota->status_perkawinan ?? '',
            $anggota->ayah ?? '',
            $anggota->ibu ?? '',
            $anggota->pendidikan ?? '',
            '', // DTKS(Y/T) - not in our model
            '', // SERTIFIKAT LAHIR - not in our model
            '', // AKTA NIKAH - not in our model
            '', // ASURANSI KESEHATAN - not in our model
            '', // PENDUDUK TETAP (Y/T) - not in our model
            '', // DISABILITAS - not in our model
            '', // VAKSIN1 (Y/T) - not in our model
            '', // VAKSIN2 (Y/T) - not in our model
            '', // VAKSIN3 (Y/T) - not in our model
            '', // TGL DATANG - not in our model
            '', // TGL_PINDAH - not in our model
            '', // PINDAH KE - not in our model
            '', // TGL MENINGGAL - not in our model
            '', // WAKTU MENINGGAL - not in our model
            '', // PENDATANG (Y/T) - not in our model
            '', // YATIM/PIATU/YATIMPIATU - not in our model
            '', // DUSUN - not in our model
            $anggota->rt ?? '',
            $anggota->rw ?? '',
            $anggota->desa_kelurahan ?? ($anggota->getKk->getRw->getDesa->nama_desa ?? ''),
            $anggota->kecamatan ?? '',
            $anggota->kabupaten_kota ?? '',
            $anggota->provinsi ?? '',
            $anggota->kewarganegaraan ?? '',
            // 32 empty SDGs columns
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        return $data;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function columnFormats(): array
    {
        $formats = [];

        if ($this->includeFilename) {
            // NIK column (C) and NomorKK column (D) with filename included
            $formats['C'] = NumberFormat::FORMAT_NUMBER;
            $formats['D'] = NumberFormat::FORMAT_NUMBER;
        } else {
            // NIK column (B) and NomorKK column (C) without filename
            $formats['B'] = NumberFormat::FORMAT_NUMBER;
            $formats['C'] = NumberFormat::FORMAT_NUMBER;
        }

        return $formats;
    }
}
