<script setup>
import {
	Card,
	CardHeader,
	CardTitle,
	CardDescription,
	CardContent,
} from "@/components/ui/card";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { MailWarning, MailCheck } from "lucide-vue-next";
import InputField from "../../../Components/InputField.vue";
import { Button } from "@/components/ui/button";
import { router } from "@inertiajs/vue3";

const props = defineProps({
	user: Object,
	status: String,
});

const verifyEmail = (e) => {
	router.post(
		route("verification.send"),
		{},
		{
			onStart: () => (e.target.disabled = true),
			onFinish: () => (e.target.disabled = false),
		}
	);
};
</script>

<template>
	<Card class="flex flex-col">
		<CardHeader>
			<CardTitle>Profile Information</CardTitle>
			<CardDescription class="lg:w-2/5">View your account information.</CardDescription>
		</CardHeader>

		<CardContent class="lg:w-3/5 lg:self-end flex-1">
			<Alert v-if="user.email_verified_at === null" variant="warning" class="mb-5">
				<MailWarning class="shrink-0" />
				<div>
					<AlertTitle>Reminder</AlertTitle>
					<AlertDescription>
						<p>Your email address is not verified.</p>
						<Button @click="verifyEmail" class="text-inherit p-0" variant="link">
							Verify Now
						</Button>
					</AlertDescription>
				</div>
			</Alert>

			<div class="space-y-6">
				<InputField label="Name" icon="id-badge" :modelValue="user.name" readonly />
				<InputField label="Email" icon="id-badge" :modelValue="user.email" readonly />
			</div>
		</CardContent>
	</Card>
</template>
