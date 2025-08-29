<script setup>
import { Form, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const confirmingUserDeletion = ref(false);
const passwordInput = ref('');

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    setTimeout(() => passwordInput.value.focus(), 250);
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    passwordInput.value = '';
};
</script>

<template>
    <section class="space-y-6">
        <HeadingSmall title="Delete Account" description="Once your account is deleted, all of its resources and data will be permanently deleted." />

        <div class="max-w-xl text-sm text-muted-foreground">
            <p>Before deleting your account, please download any data or information that you wish to retain.</p>
        </div>

        <div class="flex items-center gap-4">
            <Button variant="destructive" @click="confirmUserDeletion">
                Delete Account
            </Button>
        </div>

        <!-- Delete Account Confirmation Modal -->
        <div v-show="confirmingUserDeletion" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete your account?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                </p>

                <Form method="delete" :action="route('profile.destroy')" class="mt-6" v-slot="{ errors, processing }">
                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <Input
                            ref="passwordInput"
                            id="password"
                            type="password"
                            name="password"
                            class="mt-1 block w-full"
                            placeholder="Password"
                            required
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <Button type="button" variant="outline" @click="closeModal">
                            Cancel
                        </Button>
                        <Button type="submit" variant="destructive" :disabled="processing">
                            Delete Account
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </section>
</template>
