<div class="mb-3">
    <label class="mb-2 fw-bold" for="name">Name:</label>
    <input type="text" id="name" name="name" class="form-control" value="{{ $survey->name }}" required>
</div>
<div class="mb-3">
    <label class="mb-2 fw-bold" for="question">Question:</label>
    <input type="text" id="question" name="question" class="form-control" value="{{ $survey->question }}" required>
</div>
<div class="mb-3">
    <label class="mb-2 fw-bold" for="type">Type:</label>
    {{-- <input type="text" id="type" name="type" class="form-control" required> --}}
    <select name="type" id="" class="form-select">
        @foreach ($survey->types as $type)
            <option value="{{ $type['name'] }}">{{ $type['name'] }}</option>
        @endforeach
    </select>
</div>