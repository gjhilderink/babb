<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Invoice;
use App\Models\Member;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_members'    => Member::count(),
            'active_members'   => Member::where('status', 'active')->count(),
            'invoices_draft'   => Invoice::where('status', 'draft')->count(),
            'invoices_sent'    => Invoice::where('status', 'sent')->count(),
            'invoices_overdue' => Invoice::where('status', 'sent')->where('due_date', '<', today())->count(),
            'revenue_ytd'      => Invoice::where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->sum('total'),
            'outstanding'      => Invoice::whereIn('status', ['sent'])
                ->sum('total'),
        ];

        $recentInvoices = Invoice::with('member')
            ->latest()
            ->limit(5)
            ->get();

        $expiringMemberships = Member::with('membershipType')
            ->where('status', 'active')
            ->whereBetween('membership_end', [today(), today()->addDays(30)])
            ->orderBy('membership_end')
            ->limit(5)
            ->get();

        $upcomingEvents = Event::with('tasks')
            ->whereIn('status', ['concept', 'bevestigd'])
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentInvoices', 'expiringMemberships', 'upcomingEvents'));
    }
}
