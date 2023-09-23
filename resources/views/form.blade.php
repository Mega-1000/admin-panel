<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<div style="width: 70%; margin: 50px auto 0;">
    @foreach($form->elements as $element)
        @if($element->type === 'button')
            <form action="{{ route('execute-form-action', ['actionName' => $element->action, 'order' => $order->id]) }}" method="post">
                @csrf
                <button class="btn" style="background-color: {{ $element->color }}; font-size: {{ $element->size }}; width: 100%">
                    {{ $element->text }}
                </button>
            </form>
        @elseif($element->type === 'text')
            <div>
                {!! $element->text !!}
            </div>
        @elseif($element->type === 'link')
            <a href="{{ $element->action }}" target="{{ $element->new_tab ? '_blank' : '' }}" style="font-size: {{ $element->size }}; color: {{ $element->color }};">
                {{ $element->text }}
            </a>
        @endif
    @endforeach
</div>
