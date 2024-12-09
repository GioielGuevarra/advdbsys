<script setup>
import { cn } from "@/lib/utils";
import { badgeVariants } from ".";

const props = defineProps({
	variant: { type: String, required: false },
	class: { type: String, required: false },
});
</script>

<template>
	<div :class="cn(badgeVariants({ variant: props.variant }), props.class)">
		<slot />
	</div>
</template>
