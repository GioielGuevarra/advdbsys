<script setup>
defineProps({
	heroImage: String,
});
</script>

<template>
	<div class="relative w-full h-[400px] md:h-[500px] rounded-xl overflow-hidden">
		<!-- Background Image with Overlay -->
		<div
			:style="{
				backgroundImage: `url(${heroImage})`,
				backgroundSize: 'cover',
				backgroundPosition: 'center',
			}"
			class="absolute inset-0"
		>
			<!-- Optimized Gradient Overlay -->
			<div
				class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/40 to-transparent"
			></div>
		</div>

		<!-- Content -->
		<div
			class="relative h-full flex flex-col items-center justify-center p-4 md:px-14 text-center"
		>
			<h1
				class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-4 md:mb-6 text-white drop-shadow-lg"
			>
				Where your
				<span class="text-primary font-extrabold drop-shadow">Palate</span> meets
				<span class="text-primary font-extrabold drop-shadow">Stellar</span> delights.
			</h1>

			<p
				class="text-base sm:text-lg md:text-xl text-white max-w-2xl mb-6 md:mb-8 drop-shadow-md font-medium"
			>
				Browse a huge selection of our products and order in a click of a button.
			</p>
		</div>
	</div>
</template>
