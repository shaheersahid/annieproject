@props([
    'id' => '',
    'class' => 'table table-bordered dt-responsive nowrap w-100',
    'headers' => [] // array of ['label' => '', 'width' => '', 'maxWidth' => '', 'class' => '']
])

<div class="table-responsive">
    <table id="{{ $id }}" class="{{ $class }}" style="width: 100%;">
        <thead>
            <tr>
                @foreach($headers as $header)
                    @php
                        $label = is_array($header) ? ($header['label'] ?? '') : $header;
                        $width = is_array($header) ? ($header['width'] ?? null) : null;
                        $maxWidth = is_array($header) ? ($header['maxWidth'] ?? null) : null;
                        $headerClass = is_array($header) ? ($header['class'] ?? '') : '';
                        $headerId    = is_array($header) ? ($header['id'] ?? null) : null;
                    @endphp
                    <th class="{{ $headerClass }}"
                        @if($headerId) id="{{ $headerId }}" @endif
                        @if($width) style="width: {{ $width }};" @endif
                        @if($maxWidth) style="max-width: {{ $maxWidth }};" @endif>
                        {!! $label !!}
                    </th>
                @endforeach
            </tr>
        </thead>
        @if(isset($slot) && $slot->isNotEmpty())
            <tbody>
                {{ $slot }}
            </tbody>
        @endif
    </table>
</div>
