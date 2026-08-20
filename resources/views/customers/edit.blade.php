@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="customerForm()">
    <div>
        <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Customers
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Edit Customer</h2>
        <p class="text-slate-500 mt-1">Update customer and member information.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
            <div class="flex items-center gap-3 pb-2 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Main Customer</h3>
                    <p class="text-sm text-slate-500">Primary account holder details</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Customer / Family Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                        class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                        placeholder="e.g. Khan Family">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required
                        class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                        placeholder="03XX-XXXXXXX">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Gender</label>
                    <div class="flex items-center gap-6 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="male" {{ old('gender', $customer->gender) == 'male' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            <span class="text-sm text-slate-700">Male</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="female" {{ old('gender', $customer->gender) == 'female' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            <span class="text-sm text-slate-700">Female</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="other" {{ old('gender', $customer->gender) == 'other' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            <span class="text-sm text-slate-700">Other</span>
                        </label>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1.5">Address</label>
                    <textarea id="address" name="address" rows="2"
                        class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                        placeholder="Enter address">{{ old('address', $customer->address) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1.5">Notes</label>
                    <textarea id="notes" name="notes" rows="2"
                        class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                        placeholder="Any notes...">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Family Members</h3>
                        <p class="text-sm text-slate-500">Manage members under this customer</p>
                    </div>
                </div>
                <button type="button" @click="addMember()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-50 text-purple-600 font-semibold text-sm rounded-xl hover:bg-purple-100 border border-purple-200 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Add Member
                </button>
            </div>

            <template x-for="(member, index) in members" :key="index">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 relative group" x-show="member._deleted !== true" x-transition>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xs font-bold" x-text="index + 1"></span>
                            <span class="text-sm font-semibold text-slate-700" x-text="member.name || 'New Member'"></span>
                            <span x-show="member.id" class="text-xs bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full border border-emerald-200">Existing</span>
                        </div>
                        <button type="button" @click="removeMember(index)"
                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <input type="hidden" :name="'members[' + index + '][id]'" :value="member.id || ''">
                    <input type="hidden" :name="'members[' + index + '][_deleted]'" :value="member._deleted ? '1' : '0'">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Member Name <span class="text-red-500">*</span></label>
                            <input type="text" :name="'members[' + index + '][name]'" x-model="member.name" required
                                class="block w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                placeholder="Enter member name">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Gender</label>
                            <div class="flex items-center gap-5 pt-2">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" :name="'members[' + index + '][gender]'" value="male" x-model="member.gender" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700">Male</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" :name="'members[' + index + '][gender]'" value="female" x-model="member.gender" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700">Female</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" :name="'members[' + index + '][gender]'" value="other" x-model="member.gender" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700">Other</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="members.filter(m => m._deleted !== true).length === 0" class="bg-white rounded-2xl border-2 border-dashed border-slate-200 p-8 text-center">
                <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                <p class="text-sm text-slate-500">No members. Click <strong>"Add Member"</strong> to add family members.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-200">
                Update Customer
            </button>
            <a href="{{ route('customers.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all duration-200">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function customerForm() {
    return {
        members: {!! json_encode($customer->members->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'gender' => $m->gender])) !!},
        addMember() {
            this.members.push({ id: null, name: '', gender: 'male' });
        },
        removeMember(index) {
            if (this.members[index].id) {
                this.members[index]._deleted = true;
            } else {
                this.members.splice(index, 1);
            }
        }
    }
}
</script>
@endpush
@endsection
