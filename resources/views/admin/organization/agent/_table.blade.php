<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div
        class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users-cog text-blue-500"></i>
                Agents & Intermediaries
            </h3>
            <p class="text-xs text-gray-500 mt-1">Manage access agents and intermediaries</p>
        </div>

        <div class="flex items-center gap-2">
            <div class="relative">
                <i class="fas fa-search text-gray-400 text-xs absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="agentSearchInput" value="{{ $agentSearch ?? '' }}"
                    placeholder="Search name or code..."
                    class="pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg w-56 focus:ring-blue-500 focus:border-blue-500">
            </div>

            @if (!empty($agentSearch))
                <a href="{{ route('organization', array_merge(request()->except('agent_search', 'agents_page'), ['tab' => 'tab-agents'])) }}"
                    class="text-xs text-gray-500 hover:text-gray-700">
                    Clear
                </a>
            @endif

            <a href="{{ route('agents.create') }}"
                class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium px-3 py-2 rounded-lg transition">
                <i class="fas fa-plus-circle text-xs"></i>
                Add Intermediary
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        Agent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        Sub Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        Contact</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($agents as $agent)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                                    {{ strtoupper(substr($agent->name, 0, 1)) }}{{ strtoupper(substr(strrchr($agent->name, ' '), 1, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $agent->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $agent->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">
                                @if ($agent->glims_agent_code || $agent->genova_agent_code)
                                    {{ collect([$agent->glims_agent_code, $agent->genova_agent_code])->filter()->implode(' / ') }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $agent->user_category ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $agent->sub_user_category ?? 'N/A' }}</span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $agent->phone ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }" style="overflow: visible;">
                            <button @click="open = !open"
                                class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <!-- Edit -->
                                <a href="{{ route('agents.edit', $agent) }}"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-edit text-xs text-blue-500"></i>
                                    Edit
                                </a>
                                <!-- Delete -->
                                <form method="POST" action="{{ route('agents.destroy', $agent) }}"
                                    class="delete-agent-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-trash-alt text-xs text-red-500"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No agents found. Click "Add Agent" to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
        <div class="text-sm text-gray-500">
            <i class="fas fa-users mr-1"></i>
            @if ($agents->firstItem())
                Showing {{ $agents->lastItem() }} of {{ $agents->total() }} agents
            @else
                No agents found
            @endif
        </div>
        <div class="flex gap-2">
            {{ $agents->links() }}
        </div>
    </div>
</div>
