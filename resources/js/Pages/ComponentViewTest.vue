<script setup>
import Label from '@/Components/InputLabel.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue'
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { ref } from 'vue';

const text = ref(''); // ここで初期値を設定
const isChecked = ref(false);

const emitTest = () => {
    console.log('dadadada');
}

</script>

<template>
    <GuestLayout>
        <Label value="件名"></Label>
        <TextInput v-model="text" ></TextInput>
        <p>{{ text }}</p>
        <Checkbox @update:checked="emitTest" v-model:checked="isChecked"></Checkbox>
    </GuestLayout>
</template>


