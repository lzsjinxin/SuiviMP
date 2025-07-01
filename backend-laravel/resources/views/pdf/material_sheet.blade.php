<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
  .qr-big { width: 35mm; height: 35mm; float: right; margin-left: 8mm; }
  h3      { margin: 0 0 3mm 0; }
  table   { width: 100%; border-collapse: collapse; font-size: 10px; }
  th, td  { border: 1px solid #000; padding: 4px; }
  .hdr    { overflow: hidden; }         /* keep text left of QR */
  .label  { width: 35mm; display: inline-block; font-weight: bold; }
</style>
</head>
<body>

{{-- HEADER + QR ----------------------------------------------------}}
<div class="hdr">
  <h3>Fiche Matière Première</h3>

  <span class="label">Arrivage N° :</span> {{ $mat->arrival->id}} - {{\Carbon\Carbon::parse($mat->arrival->date)->format('d/m/Y')}}<br>
  <span class="label">Lot :</span> {{ $mat->materialBatch->batch_number     ?? '—' }}<br>
  <span class="label">Numéro:</span> {{ $mat->num             ?? '—' }}<br>
  <span class="label">Statut :</span> {{ $mat->materialStatus->status      ?? '—' }}<br>
  <span class="label">Type :</span> {{ $mat->materialType->type        ?? '—' }}<br>
  <span class="label">Emplacement :</span> {{  $mat->location->name     ?? '—' }}<br>
  <span class="label">Quantité Restante :</span> {{ $mat->qty      ?? '—' }} {{$mat->unit->title ?? '-'}}<br>
  <span class="label">Date d’exp. :</span> {{ \Carbon\Carbon::parse($mat->expiry_date)->format('d/m/Y') ?? '—' }}<br>
</div>

<img class="qr-big"
     src="data:image/png;base64,{{ base64_encode(
        QrCode::format('svg')->size(250)->generate('MAT:'.$mat->id)
     ) }}">

<div style="clear:both; margin-top: 4mm;"></div>

{{-- MOVEMENT HISTORY TABLE ----------------------------------------}}
<h4>Historique des mouvements</h4>
<table>
  <tr>
    <th>Date</th>
    <th>Origine</th>
    <th>Destination</th>
    <th>type</th>
  </tr>

  @forelse($mat->materialMovementHistories as $mv)
    <tr>
      <td>{{ \Carbon\Carbon::parse($mv->mouvement_date)->format('d/m/Y H:i') }}</td>
      <td>{{ $mv->locationFrom->name ?? '—' }}</td>
      <td>{{ $mv->locationTo->name   ?? '—' }}</td>
      <td>{{ $mv->movementType->definition ?? '—' }}</td>
    </tr>
  @empty
    <tr><td colspan="4" align="center">Aucun mouvement enregistré</td></tr>
  @endforelse
</table>

</body>
</html>
