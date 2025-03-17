<script setup>
import { reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia';

defineProps({
    errors: Object // laravelでバリデーションすればエラーが返ってくる
})

const form = reactive({
    title: null,
    content: null
})

const submitFunction = () => {
    Inertia.post('/inertia', form)
}

</script>
<template>
    <form @submit.prevent="submitFunction">
        <input type="text" v-model="form.title"><br>
        <div class="text-red-400" v-if="errors.title">{{ errors.title }}</div>

        <input type="text" v-model="form.content"><br>
        <div class="text-red-400" v-if="errors.content">{{ errors.content }}</div>
        <button>送信</button>
    </form>
</template>
