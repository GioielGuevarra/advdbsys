<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
	<div class="min-h-screen bg-background p-4 antialiased">
		<div class="flex flex-col items-center my-10">
			<Link
				:href="route('home')"
				class="text-4xl font-bold text-primary tracking-tight hover:opacity-80 transition-opacity"
			>
				MELIORAE
			</Link>
		</div>
		<main class="container mx-auto">
			<slot />
		</main>
	</div>
</template>
