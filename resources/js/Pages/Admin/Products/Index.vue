<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from "@/components/ui/select";
import {
	Dialog,
	DialogContent,
	DialogHeader,
	DialogTitle,
	DialogDescription,
	DialogFooter,
} from "@/components/ui/dialog";
import { ref, watch, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "@/Components/ui/toast/use-toast";
import { Toaster } from "@/components/ui/toast";

defineOptions({ layout: AdminLayout });

const props = defineProps({
	products: Object,
	categories: Array,
	filters: Object,
});

const search = ref(props.filters.search || "");
const categoryFilter = ref(props.filters.category || "null");

const form = useForm({
	product_name: "",
	description: "",
	price: "",
	category: "", // Changed from categories array to single category
	image: null,
	quantity: 1,
	expiration_date: "",
});

const inventoryForm = useForm({
	quantity: 1,
	expiration_date: "",
});

const showAddDialog = ref(false);
const showInventoryDialog = ref(false);
const selectedProduct = ref(null);
const showDeleteDialog = ref(false);
const productToDelete = ref(null);

// Add these new form and dialog refs after other refs
const showEditDialog = ref(false);
const editForm = useForm({
	product_name: "",
	description: "",
	price: "",
	category: "", // Add category field
	image: null, // Add new image field
});

function debounce(fn, delay) {
	let timeoutId;
	return (...args) => {
		clearTimeout(timeoutId);
		timeoutId = setTimeout(() => fn(...args), delay);
	};
}

const updateFilters = debounce(() => {
	router.get(
		route("admin.products.index"),
		{ search: search.value || null, category: categoryFilter.value || null },
		{ preserveState: true, preserveScroll: true, replace: true }
	);
}, 300);

watch([search, categoryFilter], () => {
	updateFilters();
});

const deleteProduct = () => {
	if (!productToDelete.value) return;

	router.delete(route("admin.products.destroy", productToDelete.value.id), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => {
			showDeleteDialog.value = false;
			productToDelete.value = null;
		},
	});
};

const { toast } = useToast();

const submitProduct = () => {
	// Add validation checks
	if (
		!form.product_name ||
		!form.description ||
		!form.price ||
		!form.image ||
		!form.expiration_date ||
		!form.category
	) {
		toast({
			title: "Error",
			description: "Please fill all required fields",
			variant: "destructive",
		});
		return;
	}

	// Add validation for positive numbers
	if (form.price <= 0) {
		toast({
			title: "Error",
			description: "Price must be greater than 0",
			variant: "destructive",
		});
		return;
	}

	if (form.quantity < 1) {
		toast({
			title: "Error",
			description: "Initial stock must be at least 1",
			variant: "destructive",
		});
		return;
	}

	form.post(route("admin.products.store"), {
		forceFormData: true,
		preserveScroll: true,
		onSuccess: () => {
			toast({
				title: "Success",
				description: "Product created successfully",
			});
			showAddDialog.value = false;
			form.reset();
		},
		onError: (errors) => {
			toast({
				title: "Error",
				description: Object.values(errors)[0],
				variant: "destructive",
			});
		},
	});
};

const updateInventory = () => {
	if (!selectedProduct.value) return;

	// Add validation
	if (!inventoryForm.quantity || !inventoryForm.expiration_date) {
		toast({
			title: "Error",
			description: "Please fill in both quantity and expiration date",
			variant: "destructive",
			duration: 3000,
		});
		return;
	}

	if (inventoryForm.quantity < 1) {
		toast({
			title: "Error",
			description: "Quantity must be greater than 0",
			variant: "destructive",
			duration: 3000,
		});
		return;
	}

	const today = new Date().toISOString().split("T")[0];
	if (inventoryForm.expiration_date <= today) {
		toast({
			title: "Error",
			description: "Expiration date must be after today",
			variant: "destructive",
			duration: 3000,
		});
		return;
	}

	inventoryForm.post(route("admin.products.inventory.update", selectedProduct.value.id), {
		onSuccess: () => {
			toast({
				title: "Success",
				description: `Successfully added ${inventoryForm.quantity} units to ${selectedProduct.value.name}`,
				variant: "success",
				duration: 3000,
			});
			showInventoryDialog.value = false;
			inventoryForm.reset();
			selectedProduct.value = null;
		},
		onError: (errors) => {
			toast({
				title: "Error",
				description: Object.values(errors)[0],
				variant: "destructive",
				duration: 3000,
			});
		},
	});
};

const formatID = (id) => String(id); // Convert numbers to strings

// Add form error handling
const hasErrors = computed(() => Object.keys(form.errors).length > 0);

// Add this new method after other methods
const editProduct = (product) => {
	selectedProduct.value = product; // Store the selected product first
	editForm.product_name = product.name;
	editForm.description = product.description;
	editForm.price = product.price;
	editForm.category = product.categories[0]?.id.toString();
	showEditDialog.value = true;
};

const updateProduct = () => {
	if (!selectedProduct.value) return;

	if (!editForm.product_name || !editForm.description || !editForm.category) {
		toast({
			title: "Error",
			description: "Please fill all required fields",
			variant: "destructive",
		});
		return;
	}

	if (editForm.price <= 0) {
		toast({
			title: "Error",
			description: "Price must be greater than 0",
			variant: "destructive",
		});
		return;
	}

	// Change to use router.post with _method: 'put'
	router.post(route("admin.products.update", selectedProduct.value.id), {
		_method: "put",
		...editForm.data(),
		preserveScroll: true,
		onSuccess: () => {
			toast({
				title: "Success",
				description: "Product updated successfully",
				variant: "success",
			});
			showEditDialog.value = false;
			selectedProduct.value = null;
			editForm.reset();
		},
		onError: (errors) => {
			toast({
				title: "Error",
				description: Object.values(errors)[0],
				variant: "destructive",
			});
		},
	});
};
</script>

<template>
	<Head title="| Products" />

	<div class="space-y-6">
		<!-- Header -->
		<div class="sm:flex-row sm:items-center sm:justify-between flex flex-col gap-4">
			<h2 class="text-2xl font-semibold">Products</h2>

			<!-- Actions -->
			<div class="sm:flex-row flex flex-col gap-4">
				<Input
					type="search"
					placeholder="Search products..."
					class="w-full sm:w-[250px]"
					v-model="search"
				/>

				<Select v-model="categoryFilter">
					<SelectTrigger class="w-full sm:w-[180px]">
						<SelectValue placeholder="All Categories" />
					</SelectTrigger>
					<SelectContent>
						<SelectItem value="null">All Categories</SelectItem>
						<SelectItem
							v-for="category in categories"
							:key="category.category_id"
							:value="category.category_id.toString()"
						>
							{{ category.category_name }}
						</SelectItem>
					</SelectContent>
				</Select>

				<Button @click="showAddDialog = true">Add Product</Button>
			</div>
		</div>

		<!-- Add Product Dialog -->
		<Dialog v-model:open="showAddDialog">
			<DialogContent
				class="sm:max-w-[625px] grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[90dvh]"
			>
				<DialogHeader class="p-6 pb-0">
					<DialogTitle>Add New Product</DialogTitle>
				</DialogHeader>
				<div class="px-6 py-4 overflow-y-auto">
					<form @submit.prevent="submitProduct" class="space-y-6">
						<div class="sm:grid-cols-2 grid gap-4">
							<div class="sm:col-span-2 space-y-2">
								<label>Product Name</label>
								<Input v-model="form.product_name" />
							</div>

							<div class="sm:col-span-2 space-y-2">
								<label>Description</label>
								<Textarea v-model="form.description" />
							</div>

							<div class="sm:col-span-2 space-y-2">
								<label>Category</label>
								<Select v-model="form.category">
									<SelectTrigger>
										<SelectValue placeholder="Select a category" />
									</SelectTrigger>
									<SelectContent>
										<SelectItem
											v-for="category in categories"
											:key="category.category_id"
											:value="category.category_id.toString()"
										>
											{{ category.category_name }}
										</SelectItem>
									</SelectContent>
								</Select>
							</div>

							<div class="space-y-2">
								<label>Price</label>
								<Input
									type="number"
									step="0.01"
									min="0.01"
									v-model="form.price"
									@input="(e) => (form.price = Math.abs(parseFloat(e.target.value) || 0))"
								/>
							</div>

							<div class="space-y-2">
								<label>Initial Stock</label>
								<Input
									type="number"
									min="1"
									v-model="form.quantity"
									@input="
										(e) => (form.quantity = Math.abs(parseInt(e.target.value) || 1))
									"
								/>
							</div>

							<div class="sm:col-span-2 space-y-2">
								<label>Product Image</label>
								<Input
									type="file"
									accept="image/*"
									@input="(e) => (form.image = e.target.files[0])"
								/>
							</div>

							<div class="space-y-2">
								<label>Expiration Date</label>
								<Input
									type="date"
									v-model="form.expiration_date"
									:min="new Date().toISOString().split('T')[0]"
								/>
							</div>
						</div>
					</form>
				</div>
				<DialogFooter class="p-6 pt-0">
					<Button
						type="submit"
						:disabled="form.processing || hasErrors"
						@click="submitProduct"
					>
						{{ form.processing ? "Creating Product..." : "Create Product" }}
					</Button>
				</DialogFooter>
			</DialogContent>
		</Dialog>

		<!-- Products Grid -->
		<div class="sm:grid-cols-2 lg:grid-cols-3 grid gap-6">
			<Card v-for="product in products.data" :key="product.id">
				<CardHeader class="p-0 border-b">
					<img
						:src="product.image"
						:alt="product.name"
						class="aspect-[4/3] w-full object-cover rounded-t-lg"
					/>
				</CardHeader>
				<CardContent class="p-6 space-y-4">
					<div>
						<CardTitle class="text-base">{{ product.name }}</CardTitle>
						<p class="text-muted-foreground mt-1 text-sm">
							₱{{ product.price.toLocaleString() }}
						</p>
					</div>

					<div class="flex flex-wrap gap-2">
						<span
							v-for="category in product.categories"
							:key="category.id"
							class="bg-secondary text-secondary-foreground px-2.5 py-0.5 rounded-full text-xs"
						>
							{{ category.name }}
						</span>
					</div>

					<div class="flex items-center justify-between text-sm">
						<span>Stock: {{ product.stock }}</span>
						<span
							:class="{
								'text-emerald-600': product.status === 'In Stock',
								'text-red-600': product.status === 'Out of Stock',
							}"
						>
							{{ product.status }}
						</span>
					</div>

					<div class="flex gap-2 pt-4">
						<Button variant="outline" size="sm" @click="editProduct(product)">
							Edit Product
						</Button>
						<Button
							variant="outline"
							size="sm"
							@click="
								selectedProduct = product;
								showInventoryDialog = true;
							"
						>
							Add Stock
						</Button>
						<Button
							variant="destructive"
							size="sm"
							@click="
								productToDelete = product;
								showDeleteDialog = true;
							"
						>
							Delete
						</Button>
					</div>
				</CardContent>
			</Card>
		</div>

		<!-- Inventory Dialog -->
		<Dialog v-model:open="showInventoryDialog">
			<DialogContent>
				<DialogHeader>
					<DialogTitle>Update Inventory</DialogTitle>
				</DialogHeader>
				<form @submit.prevent="updateInventory" class="space-y-4">
					<div class="space-y-2">
						<label>Product</label>
						<p class="text-muted-foreground text-sm">
							{{ selectedProduct?.name }}
						</p>
					</div>

					<div class="space-y-2">
						<label>Current Stock</label>
						<p class="text-muted-foreground text-sm">
							{{ selectedProduct?.stock }} units
						</p>
					</div>

					<div class="space-y-2">
						<label>Add Quantity <span class="text-destructive">*</span></label>
						<Input
							type="number"
							v-model="inventoryForm.quantity"
							min="1"
							placeholder="Enter quantity to add"
							:class="{ 'border-destructive': inventoryForm.errors.quantity }"
						/>
						<p v-if="inventoryForm.errors.quantity" class="text-destructive text-sm">
							{{ inventoryForm.errors.quantity }}
						</p>
					</div>

					<div class="space-y-2">
						<label>Expiration Date <span class="text-destructive">*</span></label>
						<Input
							type="date"
							v-model="inventoryForm.expiration_date"
							:min="new Date().toISOString().split('T')[0]"
							:class="{ 'border-destructive': inventoryForm.errors.expiration_date }"
						/>
						<p
							v-if="inventoryForm.errors.expiration_date"
							class="text-destructive text-sm"
						>
							{{ inventoryForm.errors.expiration_date }}
						</p>
					</div>

					<DialogFooter>
						<Button type="submit" :disabled="inventoryForm.processing">
							{{ inventoryForm.processing ? "Updating..." : "Update Stock" }}
						</Button>
					</DialogFooter>
				</form>
			</DialogContent>
		</Dialog>

		<!-- Delete Confirmation Dialog -->
		<Dialog v-model:open="showDeleteDialog">
			<DialogContent>
				<DialogHeader>
					<DialogTitle>Delete Product</DialogTitle>
					<DialogDescription>
						Are you sure you want to delete "{{ productToDelete?.name }}"? This action
						cannot be undone.
					</DialogDescription>
				</DialogHeader>
				<DialogFooter class="flex justify-end gap-2">
					<Button variant="outline" @click="showDeleteDialog = false"> Cancel </Button>
					<Button
						variant="destructive"
						@click="deleteProduct"
						:disabled="router.processing"
					>
						{{ router.processing ? "Deleting..." : "Delete" }}
					</Button>
				</DialogFooter>
			</DialogContent>
		</Dialog>

		<!-- Edit Product Dialog -->
		<Dialog v-model:open="showEditDialog">
			<DialogContent>
				<DialogHeader>
					<DialogTitle>Edit Product</DialogTitle>
				</DialogHeader>
				<form @submit.prevent="updateProduct" class="space-y-4">
					<div class="space-y-2">
						<label>Product Name</label>
						<Input v-model="editForm.product_name" />
					</div>

					<div class="space-y-2">
						<label>Description</label>
						<Textarea v-model="editForm.description" />
					</div>

					<div class="space-y-2">
						<label>Category</label>
						<Select v-model="editForm.category">
							<SelectTrigger>
								<SelectValue placeholder="Select a category" />
							</SelectTrigger>
							<SelectContent>
								<SelectItem
									v-for="category in categories"
									:key="category.category_id"
									:value="category.category_id.toString()"
								>
									{{ category.category_name }}
								</SelectItem>
							</SelectContent>
						</Select>
					</div>

					<div class="space-y-2">
						<label>Price</label>
						<Input
							type="number"
							step="0.01"
							min="0.01"
							v-model="editForm.price"
							@input="(e) => (editForm.price = Math.abs(parseFloat(e.target.value) || 0))"
						/>
					</div>

					<!-- Inside Edit Product Dialog form, add before the DialogFooter -->
					<div class="space-y-2">
						<label>Update Image (Optional)</label>
						<Input
							type="file"
							accept="image/*"
							@input="(e) => (editForm.image = e.target.files[0])"
						/>
					</div>

					<DialogFooter class="mt-4">
						<Button type="submit" :disabled="editForm.processing">
							{{ editForm.processing ? "Updating..." : "Update Product" }}
						</Button>
					</DialogFooter>
				</form>
			</DialogContent>
		</Dialog>
	</div>

	<Toaster />
</template>
