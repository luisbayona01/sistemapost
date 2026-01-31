@props([
'labelText' => null,
'id',
'required' => false,
'defaultValue' => null
])

<label for="{{$id}}" class="block text-sm font-medium text-gray-700 mb-2">
    {{$labelText ?? ucfirst($id) }}:
    <span class="text-red-600">{{ $required ? '*' : '' }}</span>
</label>
<textarea
    {{$required ? 'required' : ''}}
    rows="3"
    name="{{$id}}"
    id="{{$id}}"
    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{old($id,$defaultValue)}}</textarea>
@error($id)
<small class="text-red-600 text-xs mt-1 block">{{'*'.$message}}</small>
@enderror
