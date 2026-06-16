<section>
    <h3 class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Material</h3>
    <div class="mt-3 overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed divide-y divide-slate-200 text-sm">
                <colgroup>
                    <col class="w-14">
                    <col>
                    <col class="w-32 sm:w-44">
                </colgroup>
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">No</th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Material</th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="px-3 py-3 align-top text-center sm:px-4">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">{{ $index + 1 }}</span>
                            </td>
                            <td class="px-3 py-3 text-sm text-slate-700 sm:px-4">{{ $item['name'] }}</td>
                            <td class="px-3 py-3 text-sm font-medium text-slate-800 sm:px-4">{{ $item['qty'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
