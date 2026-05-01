<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->canAccessH1Report(), 403, 'Anda tidak memiliki hak akses untuk melihat rekapan daftar kegiatan.');

        // Default to tomorrow to tomorrow if not specified
        $startDateStr = $request->input('start_date', Carbon::tomorrow()->format('Y-m-d'));
        $endDateStr = $request->input('end_date', Carbon::tomorrow()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = Carbon::parse($endDateStr)->endOfDay();
        
        $activities = Activity::whereDate('start_date', '<=', $endDate)
                              ->whereDate('end_date', '>=', $startDate)
                              ->visibleToUser(auth()->user()) // Apply Visibility Scope
                              ->orderBy('start_date', 'asc')
                              ->orderBy('start_time', 'asc')
                              ->get();
                              
        // Generate Report Text
        $reportText = $this->generateWhatsAppText($startDate, $endDate, $activities);

        return view('reports.h1', compact('startDateStr', 'endDateStr', 'reportText', 'activities'));
    }

    public function visualH1(Request $request)
    {
        // Default to tomorrow to tomorrow if not specified
        $startDateStr = $request->input('start_date', Carbon::tomorrow()->format('Y-m-d'));
        $endDateStr = $request->input('end_date', Carbon::tomorrow()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = Carbon::parse($endDateStr)->endOfDay();
        
        $activities = Activity::whereDate('start_date', '<=', $endDate)
                              ->whereDate('end_date', '>=', $startDate)
                              ->visibleToUser(auth()->user()) // Apply Visibility Scope
                              ->orderBy('start_date', 'asc')
                              ->orderBy('start_time', 'asc')
                              ->get();

        return view('reports.h1_visual', compact('startDateStr', 'endDateStr', 'activities'));
    }

    private function generateWhatsAppText($start, $end, $activities)
    {
        // Locale settings
        Carbon::setLocale('id');
        
        if ($start->isSameDay($end)) {
            $headerDate = $start->isoFormat('dddd, D MMMM YYYY');
            $text = "Agenda DJSN untuk *{$headerDate}*. Kegiatan telah terinput di Google Calender, berikut terlampir :\n\n";
        } else {
            $startStr = $start->isoFormat('D MMMM YYYY');
            $endStr = $end->isoFormat('D MMMM YYYY');
            $text = "Agenda DJSN untuk periode *{$startStr} s.d. {$endStr}*. Kegiatan telah terinput di Google Calender, berikut terlampir :\n\n";
        }

        if ($activities->isEmpty()) {
            $text .= "Tidak ada kegiatan terjadwal pada periode tersebut.\n";
            return $text;
        }

        $allDispositionNames = $activities
            ->pluck('disposition_to')
            ->filter(fn ($names) => is_array($names) && !empty($names))
            ->flatten()
            ->unique()
            ->values();

        $dispositionUsers = User::with('division')
            ->whereIn('name', $allDispositionNames)
            ->get()
            ->keyBy('name');

        foreach ($activities as $index => $activity) {
            $num = $index + 1;
            $typeStr = $activity->type === 'external' ? 'Kegiatan Eksternal' : 'Kegiatan Internal';
            
            // Item Header
            $text .= "*{$num}) {$typeStr}*\n";
            
            // Name/Description (usually "Undangan dari...")
            // If name doesn't start with "Undangan", just print name.
            // Example splits "Undangan dari Kemenko PM" and "terkait ...".
            // Our `name` field usually contains proper subject.
            $text .= "{$activity->name}, yang akan diselenggarakan pada:\n\n";
            
            // Date Line
            // User example: "Hari, tanggal : Selasa, 9 Desember 2025"
            // If we only have single date:
            // Date Line
            // User example: "Hari, tanggal : Selasa, 9 Desember 2025" or "Senin-Jumat, 9-13 Desember 2025"
            $start = Carbon::parse($activity->start_date);
            $end = Carbon::parse($activity->end_date);
            
            $dateLine = $start->isoFormat('dddd, D MMMM YYYY');
            
            if ($activity->start_date != $activity->end_date) {
                 // Check if same month/year for concise formatting?
                 // Simple approach: "Senin-Jumat, 9-13 Desember 2025"
                 // Or just full range: "Senin, 9 Desember 2025 - Jumat, 13 Desember 2025"
                 // User example: "Senin-Jumat, 8-12 Desember 2025"
                 
                 $dayRange = $start->isoFormat('dddd') . '-' . $end->isoFormat('dddd');
                 $dateRange = $start->day . '-' . $end->isoFormat('D MMMM YYYY');
                 // If different months?
                 if ($start->month != $end->month) {
                     $dateRange = $start->isoFormat('D MMMM') . ' - ' . $end->isoFormat('D MMMM YYYY');
                 }
                 
                 $dateLine = "{$dayRange}, {$dateRange}";
            }
            
            $text .= "Hari, tanggal : {$dateLine}\n";
            
            // Time Line
            // User example: "07.30 WIB s.d. Selesai"
            $startTime = Carbon::parse($activity->start_time)->format('H.i');
            $endTime = $activity->end_time ? Carbon::parse($activity->end_time)->format('H.i') : 'Selesai';
            
            $timeLine = "{$startTime} WIB s.d. {$endTime}";
            if ($endTime != 'Selesai') {
                 $timeLine .= " WIB";
            }
            
            $text .= "Waktu : {$timeLine}\n";
            
            // Location/Media Line
            if ($activity->location_type === 'online') {
                $text .= "Media : Zoom Meeting\n";
                if ($activity->meeting_link) {
                    $text .= "Link : {$activity->meeting_link}\n"; 
                }
                if ($activity->meeting_id) {
                    $text .= "Meeting ID : {$activity->meeting_id}\n"; 
                }
                if ($activity->passcode) {
                    $text .= "Passcode : {$activity->passcode}\n"; 
                }
            } elseif ($activity->location_type === 'hybrid') {
                 $text .= "Tempat : " . ($activity->location ?? '-') . "\n";
                 $text .= "Media : Zoom Meeting (Hybrid)\n";
                 if ($activity->meeting_link) {
                    $text .= "Link : {$activity->meeting_link}\n"; 
                 }
                 if ($activity->meeting_id) {
                    $text .= "Meeting ID : {$activity->meeting_id}\n"; 
                 }
                 if ($activity->passcode) {
                    $text .= "Passcode : {$activity->passcode}\n"; 
                 }
            } else {
                // Offline
                $text .= "Tempat : " . ($activity->location ?? '-') . "\n";
            }
            
            $text .= "\n";
            
            $targets = [];
            if (!empty($activity->disposition_to) && is_array($activity->disposition_to)) {
                foreach ($activity->disposition_to as $targetName) {
                    $user = $dispositionUsers->get($targetName);
                    $targetLabel = $this->resolveReportTargetLabel($user, $targetName);

                    if ($targetLabel !== null && !in_array($targetLabel, $targets, true)) {
                        $targets[] = $targetLabel;
                    }
                }
            }

            $targetStr = empty($targets) ? '-' : implode(', ', $targets);
            
            $text .= "Kegiatan ditujukan untuk : {$targetStr}\n\n";
        }
        
        $text .= "Demikian disampaikan, terima kasih.";
        
        return $text;
    }

    private function resolveReportTargetLabel(?User $user, string $fallbackName): ?string
    {
        if (!$user) {
            return $fallbackName;
        }

        return $user->resolved_report_target_label ?? $fallbackName;
    }
}
