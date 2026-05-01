@extends('layouts.app')

@section('title', 'Dashboard Tindak Lanjut')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="mb-2 page-title">Dashboard Tindak Lanjut Kegiatan</h2>
        <p class="text-muted mb-4">Monitoring status tindak lanjut dan arahan hasil kegiatan.</p>

        @livewire('follow-up-dashboard')
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const realtimeTopics = ['followups', 'followup-dashboard'];

        const matchesFollowUpTopic = (detail) => {
            if (window.scheduloRealtime?.matchesAnyTopic) {
                return window.scheduloRealtime.matchesAnyTopic(detail, realtimeTopics);
            }

            return Array.isArray(detail?.topics)
                && detail.topics.some((topic) => realtimeTopics.includes(topic));
        };

        const refreshFollowUpDashboard = () => {
            const refresh = () => {
                window.Livewire?.dispatch?.('followup-realtime-refresh');
            };

            if (window.scheduloRealtime?.queueRefresh) {
                window.scheduloRealtime.queueRefresh('follow-up-dashboard', refresh, { delay: 250 });
                return;
            }

            window.setTimeout(refresh, 250);
        };

        window.Schedulo = {
            ...(window.Schedulo || {}),
        };

        if (window.Schedulo.followUpDashboardRealtimeRegistered) {
            return;
        }

        window.Schedulo.followUpDashboardRealtimeRegistered = true;

        window.addEventListener('schedulo:realtime', (event) => {
            const detail = event.detail ?? {};

            if (!matchesFollowUpTopic(detail)) {
                return;
            }

            refreshFollowUpDashboard();
        });
    })();
</script>
@endpush
