<ol class="pf-timeline">
    @foreach($items as $item)
        @php($bullets = $item->bullets($locale))
        <li @class(['pf-tl-row', 'is-current' => $item->isOngoing(), 'is-education' => $item->kind === 'education'])>
            <div class="pf-tl-when">
                <span class="pf-tl-period">{{ $item->periodLabel($locale) }}</span>
                @if($item->kind === 'work' && $item->durationLabel($locale))
                    <span class="pf-tl-duration">{{ $item->durationLabel($locale) }}</span>
                @endif
            </div>
            <div class="pf-tl-body">
                @if($item->company)
                    <p class="pf-tl-org">{{ $item->company }}</p>
                @endif
                <h3>{{ $item->localized('title', $locale) }}</h3>
                @if(count($bullets) > 1)
                    <ul class="pf-tl-points">
                        @foreach($bullets as $bullet)
                            <li>{{ $bullet }}</li>
                        @endforeach
                    </ul>
                @elseif(count($bullets) === 1)
                    <p class="pf-tl-desc">{{ $bullets[0] }}</p>
                @endif
                @if(! empty($item->tech_stack))
                    <div class="pf-stack">
                        @foreach($item->tech_stack as $tech)
                            <span>{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </li>
    @endforeach
</ol>
