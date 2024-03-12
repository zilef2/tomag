<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watchEffect, reactive, onMounted, watch } from 'vue';
import vSelect from "vue-select"; import "vue-select/dist/vue-select.css";
import VueDatePicker from '@vuepic/vue-datepicker';
import {
    PlusCircleIcon,XCircleIcon,
} from "@heroicons/vue/24/solid";

// <!--<editor-fold desc="Parte repetida">-->
const props = defineProps({
    show: Boolean,
    title: String,
    generica: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    centros : [0],
    valido: true,
    mensajeError : ''
})

//very usefull
const justNames = props.titulos.map(names => names['order'] )
const form = useForm({ ...Object.fromEntries(justNames.map(field => [field, ''])),
    centro_id: [props.losSelect[0]]
});
onMounted(() => {});

const printForm =[];
props.titulos.forEach(names => {
    printForm.push ({
        idd: names['order'], label: names['label'], type: names['type'],
        value: form[names['order']]
    })
});
// <!--</editor-fold>-->


function nuevoHijo(){
    data.centros.push(0)
    form.centro_id.push(props.losSelect[0])
}
let menosHijo = () => {
    data.centros.length = data.centros.length - 1
    form.centro_id.length = form.centro_id.length - 1
}

function MapearSelectDeCentros(){
    data.centros = props.generica.centro_trabajos
    data.centros.map((centro,index) => {
        let nuevoCentro = {
            title: centro.nombre,
            value: centro.id
        }
        form.centro_id[index] = nuevoCentro
    //     if(props.generica.id === centro.id){
    //         form.centro_id ={
    //             title: centro.nombre,
    //             value: centro.id
    //         }
    //     }
        return nuevoCentro
    })
}
watchEffect(() => {
    if (props.show) {
        // data.justNames.forEach(element => {
        //     form[element] =  props.generica[element]
        // });
        form.errors = {}
        props.titulos.forEach(names => {
            form[names['order']] = props.generica[names['order']]
        });
    }else{
        form.centro_id[0]={
            title: 'Disponibilidad no seleccionada',
            value: 0
        }
    }
})

watch(() => props.show, (newX) => {
    if(newX) {
        MapearSelectDeCentros();
    }
})

let validar = () => {
    try{
        data.valido = true

        if(!form.nombre || !form.tipo){
            data.valido = false
            console.log(form.nombre,'form.nombre')
            console.log(form.tipo,'form.tipo')
            throw new Error('BreakException');
        }

        form.centro_id.forEach(element => {
            if(element.value === 0){
                data.valido = false
                throw new Error('BreakException');
            }
        });
    } catch (e) {
        data.valido = false
        // if (e.message !== 'BreakException') throw e;
    }
}


const update = () => {
    validar()
    if(data.valido){
        form.put(route('disponibilidad.update', props.generica?.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    }else{
        data.mensajeError = 'Hay elementos vacios'
        alert(data.mensajeError)
    }
}
const tiposOptions = [
  {
    title: "Seleccione un tipo",
    value: 0
  },
  {
    title: "Programado",
    value: "Programado"
  },
  {
    title: "No programado",
    value: "No programado"
  },
];
</script>

<template>
    <section class="space-y-6" >
        <Modal :show="props.show" @close="emit('close')" >
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-56 scrollbar-sidebar">
                    <div class="">
                        <InputLabel for="nombre" :value="lang().label.nombre" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full"
                            v-model="form['nombre']" required :placeholder="nombre"
                            :error="form.errors['nombre']" />
                        <InputError class="mt-2" :message="form.errors['nombre']" />
                    </div>
                    <div >
                    <InputLabel for="tipo" :value="lang().label.Tipo" />
                    <v-select :options="tiposOptions" label="title"
                              class="mt-1 dark:bg-gray-500 rounded-lg dark:text-gray-600"
                              v-model="form.tipo"></v-select>
                    </div>

<!--                    {{form.centro_id}}-->
                    <div v-for="(centro, index) in data.centros" id="SelectVue">
                        <label name="labelSelectVue"
                               class="dark:text-white">
                            Centro de trabajo
<!--                            {{form.centro_id[index] ?? 'no'}}-->
                        </label>
                        <v-select :options="props.losSelect" label="title"
                                  class="dark:bg-gray-500 rounded-lg dark:text-gray-600"
                                  v-model="form.centro_id[index]"></v-select>
                        <InputError class="mt-2" :message="form.errors.centro_id" />
                    </div>
                    <div class="flex my-5 gap-8">
                        <PrimaryButton type="button" :disabled="form.processing" @click="nuevoHijo()">
                            <PlusCircleIcon class="w-6 h-6" />
                            Mas centros </PrimaryButton>
                        <PrimaryButton type="button" class="bg-red-500" :disabled="form.processing" @click="menosHijo()">
                            <XCircleIcon class="w-6 h-6" />
                            Menos centros </PrimaryButton>
                    </div>

                </div>
                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>

<style>
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}

[name="labelSelectVue"],
.muted {
    color: #1b416699;
}

[name="labelSelectVue"] {
    /* font-size: 22px; */
    font-weight: 600;
}
</style>
