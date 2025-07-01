<style>
    body{font-family:DejaVu Sans,sans-serif}
    .qr-big{width:33mm;height:33mm;float:right;margin-bottom:48mm}
    table{width:100%;border-collapse:collapse;margin-top:50mm;font-size:9.5px}
    th,td{border:1px solid #000;padding:4px}
</style>

<h3>Fiche Produit</h3>
<b>Produit :</b> {{ $product->title }}<br>
<b>S/N :</b> {{ $po->serial_number }}<br><br>

{{-- Large QR for the unit --}}

<img class="qr-big"
     src="data:image/png;base64,{{ base64_encode(
        QrCode::format('svg')->size(250)->generate('PO:'.$po->id)
     ) }}">

<h4>Opérations</h4>
<table>
    <tr>
        <th>#</th>
        <th>Opération</th>
        <th>QR</th>
        <th>Début</th>
        <th>Fin</th>
        <th>Matière</th>
        <th>Par</th>
    </tr>

    @foreach ($rows as $row)
        @php
            $def = $row['def'];
            $op  = $row['op'];                 /* may be null */
            $mat = $op?->material;
        @endphp
        <tr>
            <td align="center">{{ $row['order'] }}</td>
            <td>{{ $def->name ?? $def->title }}</td>

            {{-- mini QR --}}
            <td align="center">

                <img style="width:14mm;height:14mm;"
                     src="data:image/png;base64,{{ base64_encode(
                    QrCode::format('svg')->size(250)->generate('OP:'.$def->id)
                 ) }}">
            </td>

            <td>{{ $op?->start ?? '—' }}</td>
            <td>{{ $op?->end  ?? '—' }}</td>

            <td>
                @if($mat)
                    {{ $mat->num ?? '—' }}
                    @if($mat->materialBatch)
                        (Lot {{ $mat->materialBatch->batch_number }})
                    @endif
                @else
                    —
                @endif
            </td>

            <td>{{ $op?->userAdd?->name ?? '—' }}</td>
        </tr>
    @endforeach
</table>
<div style="clear:both"></div>
