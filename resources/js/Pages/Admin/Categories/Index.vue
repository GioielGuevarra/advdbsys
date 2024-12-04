<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import {
	Dialog,
	DialogContent,
	DialogDescription,
	DialogFooter,
	DialogHeader,
	DialogTitle,
} from "@/components/ui/dialog";
import { useToast } from "@/components/ui/toast/use-toast";

defineOptions({ layout: AdminLayout });

const props = defineProps({
	categories: Array,
});

const showAddDialog = ref(false);
const showEditDialog = ref(false);
const showDeleteDialog = ref(false);
const selectedCategory = ref(null);

const form = useForm({
	category_name: "",
	description: "",
});

const editForm = useForm({
	category_name: "",
	description: "",
});

const { toast } = useToast();

const submitCategory = () => {
	form.post(route("admin.categories.store"), {
		preserveScroll: true,
		onSuccess: (response) => {
			showAddDialog.value = false;
			form.reset();
			if (response?.props?.message) {
				toast({
					title: response.props.message.type === "success" ? "Success" : "Error",
					description: response.props.message.text,
					variant: response.props.message.type,
				});
			}
		},
	});
};

const editCategory = (category) => {
	selectedCategory.value = category;
	editForm.category_name = category.name;
	editForm.description = category.description;
	showEditDialog.value = true;
};

const updateCategory = () => {
	editForm.put(route("admin.categories.update", selectedCategory.value.id), {
		preserveScroll: true,
		onSuccess: (response) => {
			showEditDialog.value = false;
			selectedCategory.value = null;
			editForm.reset();
			if (response?.props?.message) {
				toast({
					title: response.props.message.type === "success" ? "Success" : "Error",
					description: response.props.message.text,
					variant: response.props.message.type,
				});
			}
		},
	});
};

const confirmDelete = (category) => {
	selectedCategory.value = category;
	showDeleteDialog.value = true;
};

const deleteCategory = () => {
	router.delete(route("admin.categories.destroy", selectedCategory.value.id), {
		preserveScroll: true,
		preserveState: true, // Add this to preserve state
		onSuccess: (response) => {
			showDeleteDialog.value = false;
			selectedCategory.value = null;
			if (response?.props?.message) {
				toast({
					title: response.props.message.type === "success" ? "Success" : "Error",
					description: response.props.message.text,
					variant: response.props.message.type,
				});
			}
		},
		onError: (errors) => {
			toast({
				title: "Error",
				description: errors.message || "Failed to delete category",
				variant: "destructive",
			});
		},
	});
};
</script>

<template>
	<Head title="| Categories" />

	<div class="space-y-6">
		<!-- Header -->
		<div class="flex items-center justify-between">
			<h2 class="text-2xl font-semibold">Categories</h2>
			<Button @click="showAddDialog = true">Add Category</Button>
		</div>

		<!-- Categories Grid -->
		<div class="sm:grid-cols-2 lg:grid-cols-3 grid gap-6">
			<Card v-for="category in categories" :key="category.id">
				<CardHeader>
					<CardTitle>{{ category.name }}</CardTitle>
				</CardHeader>
				<CardContent class="space-y-4">
					<p class="text-muted-foreground text-sm">
						{{ category.description }}
					</p>
					<div class="flex items-center justify-between text-sm">
						<span>{{ category.products_count }} products</span>
						<span class="text-muted-foreground">Added {{ category.created_at }}</span>
					</div>
					<div class="flex gap-2 pt-4">
						<Button variant="outline" size="sm" @click="editCategory(category)">
							Edit
						</Button>
						<Button variant="destructive" size="sm" @click="confirmDelete(category)">
							Delete
						</Button>
					</div>
				</CardContent>
			</Card>
		</div>

		<!-- Add Category Dialog -->
		<Dialog v-model:open="showAddDialog">
			<DialogContent>
				<DialogHeader>
					<DialogTitle>Add New Category</DialogTitle>
				</DialogHeader>
				<form @submit.prevent="submitCategory" class="space-y-4">
					<div class="space-y-2">
						<label>Category Name</label>
						<Input v-model="form.category_name" />
					</div>
					<div class="space-y-2">
						<label>Description</label>
						<Textarea v-model="form.description" />
					</div>
					<DialogFooter>
						<Button type="submit" :disabled="form.processing">
							{{ form.processing ? "Creating..." : "Create Category" }}
						</Button>
					</DialogFooter>
				</form>
			</DialogContent>
		</Dialog>

		<!-- Edit Category Dialog -->
		<Dialog v-model:open="showEditDialog">
			<DialogContent>
				<DialogHeader>
					<DialogTitle>Edit Category</DialogTitle>
				</DialogHeader>
				<form @submit.prevent="updateCategory" class="space-y-4">
					<div class="space-y-2">
						<label>Category Name</label>
						<Input v-model="editForm.category_name" />
					</div>
					<div class="space-y-2">
						<label>Description</label>
						<Textarea v-model="editForm.description" />
					</div>
					<DialogFooter>
						<Button type="submit" :disabled="editForm.processing">
							{{ editForm.processing ? "Updating..." : "Update Category" }}
						</Button>
					</DialogFooter>
				</form>
			</DialogContent>
		</Dialog>

		<!-- Delete Confirmation Dialog -->
		<Dialog v-model:open="showDeleteDialog">
			<DialogContent>
				<DialogHeader>
					<DialogTitle>Delete Category</DialogTitle>
					<DialogDescription>
						Are you sure you want to delete "{{ selectedCategory?.name }}"? This action
						cannot be undone.
					</DialogDescription>
				</DialogHeader>
				<DialogFooter>
					<Button variant="destructive" @click="deleteCategory"> Delete Category </Button>
				</DialogFooter>
			</DialogContent>
		</Dialog>
	</div>
</template>
