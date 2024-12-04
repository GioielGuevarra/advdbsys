<script setup>
import {
	createTable,
	getCoreRowModel,
	getFilteredRowModel,
	getPaginationRowModel,
	getSortedRowModel,
} from "@tanstack/vue-table";
import { computed } from "vue";

const props = defineProps({
	data: {
		type: Array,
		required: true,
	},
	columns: {
		type: Array,
		required: true,
	},
});

const table = computed(() =>
	createTable({
		data: props.data,
		columns: props.columns,
		getCoreRowModel: getCoreRowModel(),
		getSortedRowModel: getSortedRowModel(),
		getPaginationRowModel: getPaginationRowModel(),
		getFilteredRowModel: getFilteredRowModel(),
	})
);
</script>

<template>
	<div class="w-full overflow-x-auto">
		<table class="w-full min-w-[600px] caption-bottom">
			<thead>
				<tr class="border-b">
					<th
						v-for="column in columns"
						:key="column.header"
						class="h-11 px-4 text-left align-middle text-sm font-medium text-muted-foreground"
					>
						{{ column.header }}
					</th>
				</tr>
			</thead>
			<tbody>
				<tr
					v-for="row in data"
					:key="row.id"
					class="border-b transition-colors hover:bg-muted/50"
				>
					<td
						v-for="column in columns"
						:key="column.header"
						class="p-4 align-middle text-sm"
					>
						<span
							v-if="column.accessorKey === 'status'"
							class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
							:class="{
								'bg-emerald-100 text-emerald-700': row.status === 'completed',
								'bg-yellow-100 text-yellow-700': row.status === 'pending',
								'bg-red-100 text-red-700': row.status === 'cancelled',
							}"
						>
							{{ row[column.accessorKey] }}
						</span>
						<span v-else-if="column.accessorKey === 'total_amount'">
							₱{{ row[column.accessorKey].toLocaleString() }}
						</span>
						<span v-else>
							{{ row[column.accessorKey] }}
						</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>
