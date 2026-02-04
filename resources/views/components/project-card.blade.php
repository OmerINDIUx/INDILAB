@props(['project', 'showCategory' => true, 'showDate' => true, 'showEdit' => false])

<article class="project-card-v2" data-tags="{{ $project->category ?? 'General' }}">
    @if($project->coming_soon)
        <div class="project-card-v2__inner coming-soon-card">
    @else
        <a href="{{ route('work.show', $project) }}" class="project-card-v2__inner">
    @endif

        <div class="project-card-v2__thumb-wrapper">
            @if($project->image_path)
                <img src="{{ asset('storage/' . $project->image_path) }}" alt="{{ $project->title }}" class="project-card-v2__thumb" />
            @else
                <img src="{{ asset('img/1x/Mesa de trabajo 2.png') }}" alt="{{ $project->title }}" class="project-card-v2__thumb" />
            @endif
            
            @if($showCategory && $project->category)
                <span class="project-card-v2__badge {{ $project->badge_color ?? 'cat-grad-1' }}">{{ $project->category }}</span>
            @endif
        </div>

        <div class="project-card-v2__info">
            <h3 class="project-card-v2__title">
                {{ \Illuminate\Support\Str::limit($project->title, 50, '...') }}
            </h3>
            
            <div class="project-card-v2__meta">
                @if($showDate)
                    <p class="project-card-v2__date">
                        {{ $project->coming_soon ? 'PRÓXIMAMENTE' : ($project->published_at ? $project->published_at->format('F Y') : 'Publicado') }}
                    </p>
                @endif

                @if($showEdit)
                    @auth
                        <div style="display: flex; gap: 8px;">
                            <button type="button" 
                                class="project-card-v2__edit-btn" 
                                data-id="{{ $project->slug }}"
                                data-has-draft="{{ $project->draft_content ? 'true' : 'false' }}"
                                data-draft-date="{{ $project->draft_updated_at ? $project->draft_updated_at->format('d/m/Y H:i') : '' }}"
                                data-draft-user="{{ $project->lastEditor ? $project->lastEditor->name : 'Sistema' }}"
                                data-edit-url="{{ route('work.edit', $project) }}"
                                onclick="handleEditClick(this); event.preventDefault(); event.stopPropagation();">
                                Editar
                            </button>
                            
                            <form action="{{ route('work.destroy', $project) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer.')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="project-card-v2__delete-btn" title="Eliminar proyecto" onclick="event.stopPropagation();">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endauth
                @endif
            </div>
        </div>

    @if($project->coming_soon)
        </div>
    @else
        </a>
    @endif
</article>
