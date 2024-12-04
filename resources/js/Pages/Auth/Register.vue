<script setup>
import TextLink from "../../Components/TextLink.vue";
import InputField from "../../Components/InputField.vue";
import { Button } from "@/components/ui/button";
import { useForm } from "@inertiajs/vue3";
import AuthLayout from "../../Layouts/AuthLayout.vue";

defineOptions({ layout: AuthLayout });

const form = useForm({
	first_name: "",
	last_name: "",
	email: "",
	password: "",
	password_confirmation: "",
});

const submit = () => {
	form.post(route("register"), {
		onSuccess: () => form.reset("password", "password_confirmation"),
		onError: () => {
			if (form.errors.password) {
				form.reset("password", "password_confirmation");
			}
		},
	});
};
</script>

<template>
	<Head title="| Register" />
	<div class="max-w-md mx-auto mt-20">
		<div class="mb-8 text-center">
			<h1 class="mb-2 text-3xl font-bold">Register a new account</h1>
			<p>
				Already have an account?
				<TextLink routeName="login" label="Login" />
			</p>
		</div>

		<form @submit.prevent="submit" class="space-y-6">
			<InputField
				label="First Name"
				icon="id-badge"
				v-model="form.first_name"
				placeholder="First Name"
				:error="form.errors.first_name"
			/>

			<InputField
				label="Last Name"
				icon="id-badge"
				v-model="form.last_name"
				placeholder="Last Name"
				:error="form.errors.last_name"
			/>

			<InputField
				label="Email"
				icon="envelope"
				v-model="form.email"
				placeholder="email@example.com"
				:error="form.errors.email"
			/>

			<InputField
				label="Password"
				type="password"
				icon="key"
				v-model="form.password"
				placeholder="must be at least 8 characters"
				:error="form.errors.password"
			/>

			<InputField
				label="Confirm Password"
				type="password"
				icon="key"
				v-model="form.password_confirmation"
				placeholder="must be at least 8 characters"
				:error="form.errors.password"
			/>

			<div class="text-muted-foreground text-sm" v-if="form.errors.password">
				Password must contain:
				<ul class="list-disc list-inside">
					<li>At least 8 characters</li>
					<li>One uppercase letter</li>
					<li>One lowercase letter</li>
					<li>One number</li>
					<li>One special character (@$!%*?&)</li>
				</ul>
			</div>

			<p class="text-text text-sm">
				By creating an account, you agree to our
				<TextLink routeName="home" label="Terms of Service" /> and
				<TextLink routeName="home" label="Privacy Policy" />.
			</p>

			<Button :disabled="form.processing" class="w-full text-black rounded-lg" size="lg">
				Register
			</Button>
		</form>
	</div>
</template>
