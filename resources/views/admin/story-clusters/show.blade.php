@extends('admin.layouts.master')

@section('title', 'Story Cluster Detay')
@section('active', 'story-clusters')
@section('crumbs', 'Haber Toplama | Story Cluster Detay')

@section('content')
    <x-admin.page-header
        eyebrow="Hikaye Kümesi"
        title="{{ $cluster->title ?: 'Başlık yok' }}"
        subtitle="Cluster detayları ve bu kümeye ait tarama geçmişi."
    >
        <x-slot:actions>
            <x-admin.button variant="secondary" :href="route('admin.story-clusters.index')">Listeye dön</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <div class="stats-grid" style="margin-bottom: 18px;">
        <x-admin.card eyebrow="Cluster" title="Özet">
            <div class="form-grid">
                <div class="field">
                    <div class="field-label">Cluster ID</div>
                    <div class="data-cell-mono">#{{ $cluster->id }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Durum</div>
                    <span class="tag t-new">{{ $cluster->status }}</span>
                </div>
                <div class="field">
                    <div class="field-label">Skor</div>
                    <span class="badge primary">{{ $cluster->story_score }}</span>
                </div>
                <div class="field">
                    <div class="field-label">Kategori</div>
                    <div>{{ $cluster->category?->name ?: '-' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Ana kaynak</div>
                    <div>{{ $cluster->mainSourceAccount ? '@'.$cluster->mainSourceAccount->username : '-' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Son güncelleme</div>
                    <div class="data-cell-mono">{{ $cluster->last_updated_at?->format('Y-m-d H:i') ?: '-' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Sonraki tarama süresi</div>
                    <div class="data-cell-mono">{{ $cluster->mainSourceAccount?->next_check_interval_minutes ? $cluster->mainSourceAccount->next_check_interval_minutes.' dk' : '-' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Sonraki tarama</div>
                    <div class="data-cell-mono">{{ $cluster->mainSourceAccount?->next_check_at?->format('Y-m-d H:i') ?: '-' }}</div>
                </div>
            </div>
        </x-admin.card>
    </div>

    @include('admin.scan-histories._table', [
        'historyAction' => route('admin.story-clusters.show', $cluster),
        'tableTitle' => 'Cluster tarama geçmişi',
        'showClusterColumns' => false,
    ])
@endsection
