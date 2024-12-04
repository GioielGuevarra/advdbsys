
<script setup>
    import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card'
    import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
    import { MailWarning } from 'lucide-vue-next'
    import { Button } from '@/components/ui/button'
    import { router } from '@inertiajs/vue3'

    const props = defineProps({ 
        user: Object,
    });

    const verifyEmail = (e) => {
        router.post(route('verification.send'), {}, {
            onStart: () => e.target.disabled = true,
            onFinish: () => e.target.disabled = false,
        })
    }
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader>
            <CardTitle>Account Information</CardTitle>
            <CardDescription class="lg:w-2/5">Your account details.</CardDescription>
        </CardHeader>
        
        <CardContent class="lg:w-3/5 lg:self-end flex-1">
            <Alert 
                v-if="user.email_verified_at === null" 
                variant="warning" class="mb-5">
                <MailWarning class="shrink-0"/>
                <div>
                    <AlertTitle>Reminder</AlertTitle>
                    <AlertDescription>
                        <p>Your email address is not verified.</p>
                        <Button 
                            @click="verifyEmail"
                            class="text-inherit p-0" 
                            variant="link">Verify Now
                        </Button>
                    </AlertDescription>
                </div>
            </Alert>

            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium">Name</p>
                    <p class="text-muted-foreground text-sm">{{ user.name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium">Email</p>
                    <p class="text-muted-foreground text-sm">{{ user.email }}</p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>