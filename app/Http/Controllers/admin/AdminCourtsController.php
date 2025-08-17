<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BasketCourt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminCourtsController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            abort(404);
        }

        if (Auth::user()->role_id != 1) {
            abort(404); 
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Lapangan',
            'courts' => BasketCourt::latest()->get()
        ];
        return view('admin.crud.court', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:basket_courts',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|numeric|min:0',
            'photos' => 'max:20', // Validasi maksimal 20 file
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:51200' // 50MB dalam kilobytes
        ]);

        // Validasi total ukuran file
        $totalSize = 0;
        if($request->hasFile('photos')) {
            foreach($request->file('photos') as $photo) {
                $totalSize += $photo->getSize();
            }
            
            // Check if total size exceeds 50MB (52428800 bytes)
            if($totalSize > 52428800) {
                return back()->withErrors(['photos' => 'Total ukuran foto tidak boleh melebihi 50MB']);
            }
        }

        // Buat folder dengan nama lapangan (slug)
        $courtName = Str::slug($request->name);
        $folderPath = public_path("court/{$courtName}");
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Upload multiple foto
        $photoPaths = [];
        if($request->hasFile('photos')) {
            $photos = $request->file('photos');
            foreach($photos as $index => $photo) {
                if($index >= 20) break; // Maksimal 20 foto
                $photoName = time() . '_' . $index . '.' . $photo->getClientOriginalExtension();
                $photo->move($folderPath, $photoName);
                $photoPaths[] = "court/{$courtName}/{$photoName}";
            }
        }

        BasketCourt::create([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'price_per_hour' => $request->price_per_hour,
            'is_available' => true,
            'photo' => implode(',', $photoPaths) // Gabungkan path foto dengan koma
        ]);

        return redirect()->route('admin.court')->with('success', 'Lapangan berhasil ditambahkan');
    }

    public function update(Request $request, BasketCourt $court)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:basket_courts,name,' . $court->id,
            'location' => 'required|string',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|numeric|min:0',
            'photos' => 'max:20', // Validasi maksimal 20 file
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:51200' // 50MB dalam kilobytes
        ]);

        // Validasi total ukuran file
        $totalSize = 0;
        if($request->hasFile('photos')) {
            foreach($request->file('photos') as $photo) {
                $totalSize += $photo->getSize();
            }
            
            // Check if total size exceeds 50MB (52428800 bytes)
            if($totalSize > 52428800) {
                return back()->withErrors(['photos' => 'Total ukuran foto tidak boleh melebihi 50MB']);
            }
        }

        $data = [
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'price_per_hour' => $request->price_per_hour,
            'is_available' => $request->has('is_available')
        ];

        // Jika ada foto baru
        if ($request->hasFile('photos')) {
            // Hapus foto lama
            $oldPhotos = explode(',', $court->photo);
            foreach($oldPhotos as $oldPhoto) {
                if(file_exists(public_path($oldPhoto))) {
                    unlink(public_path($oldPhoto));
                }
            }

            // Upload foto baru
            $courtName = Str::slug($request->name);
            $folderPath = public_path("court/{$courtName}");
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $photoPaths = [];
            $photos = $request->file('photos');
            foreach($photos as $index => $photo) {
                if($index >= 20) break; // Maksimal 20 foto
                $photoName = time() . '_' . $index . '.' . $photo->getClientOriginalExtension();
                $photo->move($folderPath, $photoName);
                $photoPaths[] = "court/{$courtName}/{$photoName}";
            }

            $data['photo'] = implode(',', $photoPaths);

            // Hapus folder lama jika nama berubah
            $oldCourtName = Str::slug($court->name);
            if ($oldCourtName !== $courtName) {
                $oldFolderPath = public_path("court/{$oldCourtName}");
                if (file_exists($oldFolderPath)) {
                    rmdir($oldFolderPath);
                }
            }
        }

        $court->update($data);
        return redirect()->route('admin.court')->with('success', 'Lapangan berhasil diperbarui');
    }

    public function destroy(BasketCourt $court)
    {
        // Hapus foto
        $oldPhotos = explode(',', $court->photo);
        foreach($oldPhotos as $oldPhoto) {
            if(file_exists(public_path($oldPhoto))) {
                unlink(public_path($oldPhoto));
            }
        }

        // Hapus folder
        $courtName = Str::slug($court->name);
        $folderPath = public_path("court/{$courtName}");
        if (file_exists($folderPath)) {
            rmdir($folderPath);
        }

        $court->delete();
        return redirect()->route('admin.court')->with('success', 'Lapangan berhasil dihapus');
    }

    public function setHoliday(Request $request)
    {
        $request->validate([
            'holiday_date' => 'required|date|after_or_equal:today',
            'holiday_start_time' => 'required|date_format:H:i',
            'holiday_end_time' => 'required|date_format:H:i|after:holiday_start_time'
        ]);

        $courts = BasketCourt::all();
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');
        
        foreach ($courts as $court) {
            $holidayDates = $court->holiday_dates ?? [];
            if (!in_array($request->holiday_date, $holidayDates)) {
                $holidayDates[] = $request->holiday_date;
                $newStatus = ($request->holiday_date === $today && 
                             $currentTime >= $request->holiday_start_time && 
                             $currentTime <= $request->holiday_end_time) ? 'inactive' : $court->status;
                
                $court->update([
                    'holiday_dates' => $holidayDates,
                    'holiday_start_time' => $request->holiday_start_time,
                    'holiday_end_time' => $request->holiday_end_time,
                    'status' => $newStatus
                ]);

                // Update jadwal untuk tanggal yang ditentukan
                if ($newStatus === 'inactive') {
                    $court->updateScheduleStatus('inactive');
                }
            }
        }

        return redirect()->route('admin.court')->with('success', 'Tanggal dan waktu libur berhasil ditambahkan');
    }

    public function removeHoliday(Request $request)
    {
        $request->validate([
            'holiday_date' => 'required|date'
        ]);

        $courts = BasketCourt::all();
        $today = now()->format('Y-m-d');
        
        foreach ($courts as $court) {
            $holidayDates = $court->holiday_dates ?? [];
            $holidayDates = array_diff($holidayDates, [$request->holiday_date]);
            $newStatus = ($request->holiday_date === $today) ? 'active' : $court->status;
            
            $court->update([
                'holiday_dates' => array_values($holidayDates),
                'status' => $newStatus
            ]);

            // Update status jadwal jika status berubah
            if ($newStatus !== $court->status) {
                $court->updateScheduleStatus($newStatus);
            }
        }

        return redirect()->route('admin.court')->with('success', 'Tanggal libur berhasil dihapus');
    }
}
