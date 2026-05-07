<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTask;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Member;
use App\Models\Task;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $myEventTasks = EventTask::with('event')
            ->where('assigned_to', $user->name)
            ->whereIn('status', ['open', 'bezig'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $myTasks = Task::where('assigned_to_user_id', $user->id)
            ->whereIn('status', ['open', 'bezig'])
            ->orderByRaw("FIELD(priority, 'hoog', 'normaal', 'laag')")
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $myLeads = Lead::where('assigned_to_user_id', $user->id)
            ->whereNotIn('status', ['gewonnen', 'verloren'])
            ->orderByRaw("FIELD(status, 'follow_up', 'contact', 'nieuw')")
            ->limit(10)
            ->get();

        $upcomingEvents = Event::with('tasks')
            ->whereIn('status', ['concept', 'bevestigd'])
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $upcomingEventsBudget = Event::whereIn('status', ['concept', 'bevestigd'])
            ->where('event_date', '>=', now())
            ->whereNotNull('budget')
            ->sum('budget');

        if ($user->isGebruiker()) {
            return view('dashboard.index', [
                'upcomingEvents'       => $upcomingEvents,
                'upcomingEventsBudget' => $upcomingEventsBudget,
                'myTasks'              => $myTasks,
                'myEventTasks'         => $myEventTasks,
                'myLeads'              => $myLeads,
                'stats'                => null,
                'recentInvoices'       => collect(),
                'expiringMemberships'  => collect(),
                'recentLeads'          => collect(),
            ]);
        }

        $stats = [
            'total_members'    => Member::count(),
            'active_members'   => Member::where('status', 'active')->count(),
            'invoices_draft'   => Invoice::where('status', 'draft')->count(),
            'invoices_sent'    => Invoice::where('status', 'sent')->count(),
            'invoices_overdue' => Invoice::where('status', 'sent')->where('due_date', '<', today())->count(),
            'revenue_ytd'      => Invoice::where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->sum('total'),
            'outstanding'      => Invoice::whereIn('status', ['sent'])->sum('total'),
        ];

        $recentInvoices = Invoice::with('member')->latest()->limit(5)->get();

        $expiringMemberships = Member::with('membershipType')
            ->where('status', 'active')
            ->whereBetween('membership_end', [today(), today()->addDays(30)])
            ->orderBy('membership_end')
            ->limit(5)
            ->get();

        $recentLeads = Lead::with(['assignedTo'])
            ->whereNotIn('status', ['gewonnen', 'verloren'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentInvoices', 'expiringMemberships', 'upcomingEvents', 'recentLeads', 'upcomingEventsBudget', 'myTasks', 'myEventTasks', 'myLeads'));
    }
}
