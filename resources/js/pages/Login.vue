<script setup lang="ts">
import {Form, router, useForm} from "@inertiajs/vue3";
import {auth} from "@/routes/auth/login";
import {Ref, ref} from "vue";

type UserLoginData = {
    id: number;
    name: string;
}


const props = defineProps<{
    users: UserLoginData[]
}>();

function handleUserSelect(id: number) {
    router.post(auth().url, {id});
}
</script>

<template>
    <div class="flex justify-center items-center flex-1">
        <div class="md:w-96 bg-platinum p-6 pixel-box">
            <h3 class="text-xl font-bold text-onyx text-center mb-6">Kim jesteś?</h3>

            <ul class="flex flex-col gap-1">
                <li class="border-4 border-onyx cursor-pointer p-2 bg-[#e1e1e1]" v-for="user in props.users"
                    :key="user.id" @click="handleUserSelect(user.id)">{{ user.name }}
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>

</style>
