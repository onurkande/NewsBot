@extends('admin.layouts.master')

@section('title', 'Story Cluster')
@section('active', 'story-clusters')
@section('crumbs', 'Haber Toplama | Story Cluster')

@section('content')
    <section class="card">
        <div class="card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Hikaye Bazli Akis</span>
                <h2 class="card-title">Story cluster listesi</h2>
            </div>
        </div>

        <form class="data-toolbar" method="GET" action="{{ route('admin.story-clusters.index') }}">
            <div class="data-toolbar-left">
                <select class="select" name="status">
                    <option value="">Tum durumlar</option>
                    @foreach (['open' => 'Acik', 'selected' => 'Secildi', 'published' => 'Yayinlandi', 'ignored' => 'Yoksayildi'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="data-toolbar-right">
                <button class="btn btn--secondary" type="submit">Filtrele</button>
            </div>
        </form>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Story</th>
                        <th>Kategori</th>
                        <th>Ana kaynak</th>
                        <th>Tweet</th>
                        <th>Skor</th>
                        <th>Durum</th>
                        <th>Son guncelleme</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clusters as $cluster)
                        <tr>
                            <td style="white-space: normal; min-width: 360px">
                                <div class="data-cell-user-name">{{ $cluster->title ?: 'Baslik yok' }}</div>
                                <div class="data-cell-mono">{{ Str::limit($cluster->cluster_hash, 18, '') }}</div>
                            </td>
                            <td>{{ $cluster->category?->name ?: '-' }}</td>
                            <td>{{ $cluster->mainSourceAccount ? '@'.$cluster->mainSourceAccount->username : '-' }}</td>
                            <td><span class="badge info">{{ $cluster->items_count }} tweet</span></td>
                            <td><span class="badge primary">{{ $cluster->story_score }}</span></td>
                            <td><span class="tag t-new">{{ $cluster->status }}</span></td>
                            <td class="data-cell-mono">{{ $cluster->last_updated_at?->diffForHumans() ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Henüz story cluster yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="data-foot">
            <span>{{ $clusters->total() }} kayit</span>
            {{ $clusters->links() }}
        </div>
    </section>
@endsection
