<div class="mb-3">
    <label class="mb-2 fw-bold" for="name">Name:</label>
    <input type="text" id="name" name="name" class="form-control" value="{{ $device->name }}" required>
</div>
<div class="mb-3">
    <label class="mb-2 fw-bold" for="description">Description:</label>
    <input type="text" id="description" name="description" class="form-control" value="{{ $device->description }}" required>
</div>