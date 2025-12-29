<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dudi;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DudiController extends Controller
{
    /**
     * Display Dudi list
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Dudi::orderBy('nama');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('bidang_usaha', 'like', "%{$search}%");
            });
        }

        $dudis = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Dudi::count(),
        ];

        return view('admin.dudi.index', compact('dudis', 'search', 'stats'));
    }

    /**
     * Store new Dudi
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'bidang_usaha' => 'nullable|string|max:255',
        ]);

        Dudi::create($request->only(['nama', 'alamat', 'telepon', 'bidang_usaha']));

        return redirect()->route('admin.dudi.index')->with('success', 'Data Dudi berhasil ditambahkan');
    }

    /**
     * Update Dudi
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'bidang_usaha' => 'nullable|string|max:255',
        ]);

        $dudi = Dudi::findOrFail($id);
        $dudi->update($request->only(['nama', 'alamat', 'telepon', 'bidang_usaha']));

        return redirect()->route('admin.dudi.index')->with('success', 'Data Dudi berhasil diperbarui');
    }

    /**
     * Delete Dudi
     */
    public function destroy($id)
    {
        $dudi = Dudi::findOrFail($id);
        $dudi->delete();

        return redirect()->route('admin.dudi.index')->with('success', 'Data Dudi berhasil dihapus');
    }

    /**
     * Download template Excel
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'nama');
        $sheet->setCellValue('B1', 'alamat');
        $sheet->setCellValue('C1', 'telepon');
        $sheet->setCellValue('D1', 'bidang_usaha');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Sample data
        $sheet->setCellValue('A2', 'PT. Contoh Industri');
        $sheet->setCellValue('B2', 'Jl. Industri No. 1, Kota');
        $sheet->setCellValue('C2', '08123456789');
        $sheet->setCellValue('D2', 'Manufaktur');

        // Auto width
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_dudi.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import Dudi from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $imported = 0;
            $errors = [];

            // Skip header row
            foreach (array_slice($rows, 1) as $index => $row) {
                $rowNum = $index + 2;

                // Skip empty rows
                if (empty($row[0])) {
                    continue;
                }

                $nama = trim($row[0] ?? '');
                $alamat = trim($row[1] ?? '');
                $telepon = trim($row[2] ?? '');
                $bidangUsaha = trim($row[3] ?? '');

                if (empty($nama)) {
                    $errors[] = "Baris {$rowNum}: Nama tidak boleh kosong";
                    continue;
                }

                Dudi::create([
                    'nama' => $nama,
                    'alamat' => $alamat ?: null,
                    'telepon' => $telepon ?: null,
                    'bidang_usaha' => $bidangUsaha ?: null,
                ]);

                $imported++;
            }

            if (count($errors) > 0) {
                return redirect()->route('admin.dudi.index')
                    ->with('success', "{$imported} data berhasil diimport")
                    ->with('import_errors', $errors);
            }

            return redirect()->route('admin.dudi.index')->with('success', "{$imported} data Dudi berhasil diimport");
        } catch (\Exception $e) {
            return redirect()->route('admin.dudi.index')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Export Dudi to Excel
     */
    public function export()
    {
        $dudis = Dudi::orderBy('nama')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Alamat');
        $sheet->setCellValue('D1', 'Telepon');
        $sheet->setCellValue('E1', 'Bidang Usaha');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Data
        $row = 2;
        foreach ($dudis as $index => $dudi) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $dudi->nama);
            $sheet->setCellValue('C' . $row, $dudi->alamat);
            $sheet->setCellValue('D' . $row, $dudi->telepon);
            $sheet->setCellValue('E' . $row, $dudi->bidang_usaha);
            $row++;
        }

        // Auto width
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'data_dudi_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export Dudi to PDF
     */
    public function exportPdf()
    {
        $dudis = Dudi::orderBy('nama')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.dudi.pdf', compact('dudis'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('data_dudi_' . date('Y-m-d') . '.pdf');
    }
}
