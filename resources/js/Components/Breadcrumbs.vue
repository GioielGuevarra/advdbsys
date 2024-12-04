<script setup>
import {
	Breadcrumb,
	BreadcrumbItem,
	BreadcrumbLink,
	BreadcrumbList,
	BreadcrumbPage,
	BreadcrumbSeparator,
} from "@/components/ui/breadcrumb";

defineProps({ category: String, currentPage: String });
</script>

<template>
	<Breadcrumb>
		<BreadcrumbList>
			<BreadcrumbItem>
				<BreadcrumbLink :href="route('home')">Home</BreadcrumbLink>
			</BreadcrumbItem>

			<BreadcrumbSeparator />

			<BreadcrumbItem v-if="category">
				<BreadcrumbLink :href="route('category.show', category)">
					{{ category }}
				</BreadcrumbLink>
			</BreadcrumbItem>

			<BreadcrumbSeparator v-if="category" />

			<BreadcrumbItem>
				<BreadcrumbPage>{{ currentPage }}</BreadcrumbPage>
			</BreadcrumbItem>
		</BreadcrumbList>
	</Breadcrumb>
</template>
