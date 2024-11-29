<script setup>
import { Button } from "@/components/ui/button";

defineProps({
	CTAImage: String,
});
</script>

<template>
	<div
		:style="{
			backgroundImage: `linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(${CTAImage})`,
			backgroundSize: 'cover',
			backgroundPosition: 'center',
			backgroundRepeat: 'no-repeat',
		}"
		class="w-full h-auto bg-cover bg-center rounded-xl grid place-items-center"
	>
		<!-- contents -->
		<div
			class="grid place-items-center text-center text-gray-100 px-7 py-10 md:py-20 md:px-14"
		>
			<!-- title -->
			<h1 class="text-4xl font-md tracking-tight sm:text-5xl">
				Where your
				<span class="text-primary">Palate</span> meets
				<span class="text-primary">Stellar</span> delights.
			</h1>

			<!-- desc -->
			<p class="max-w-prose mt-4">
				Browse a huge selection of our products and order in a click of a button.
			</p>

			<!-- buttons -->
			<div class="flex w-full py-4 px-4">
				<Button class="w-full text-black">Order Now!</Button>
			</div>
		</div>
	</div>
</template>
