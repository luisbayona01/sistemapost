@props([
'labelText' => null,
'id',
'required' => false,
'defaultValue' => null,
'type' => null
])

<label for="{{$id}}" class="block text-sm font-medium text-gray-700 mb-2">
    {{$labelText ?? ucfirst($id) }}:
    <span class="text-red-600">{{ $required ? '*' : '' }}</span>
</label>
<input
    {{$required ? 'required' : ''}}
    type="{{ $type ? $type : 'text'}}"
    @if ($type == 'number')
        step="0.1"
    @endif
    name="{{$id}}"
    id="{{$id}}"
    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
    value="{{old($id,$defaultValue)}}">
@error($id)
<small class="text-red-600 text-xs mt-1 block">{{'*'.$message}}</small>
@enderror
