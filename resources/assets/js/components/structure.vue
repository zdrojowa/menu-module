<template>
    <div>
        <div class="card">
            <div class="card-header clearfix">
                <h4 class="card-title float-left"><i class="mdi mdi-menu"></i> Struktura</h4>
                <div class="float-right">
                    <b-button variant="success" v-b-modal="'0'"><i class="mdi mdi-plus"></i> Dodaj</b-button>
                </div>
            </div>

            <div class="card-body">
                <nested v-model="elements" :types="types" :lang="lang"></nested>
            </div>
        </div>

        <modal id='0' :item="element" :types="types" :lang="lang" @save="add"></modal>
    </div>
</template>

<script>
    export default {
        name: "structure",
        props: {
            value: {
                required: false,
                type: Array,
                default: []
            },
            lang: {
                required: false,
                type: String,
                default: 'pl'
            }
        },

        data() {
            return {
                element: {id: 0, type: 'page', name: '', title: '', url: '', elements: []},
                elements: [],
                types: []
            };
        },

        created() {
            this.getTypes();
        },

        methods: {

            getTypes() {
                axios.get('/dashboard/menu/types')
                    .then(res => {
                        this.types = res.data;
                    }).catch(err => {
                    console.log(err)
                })
            },

            add($event) {
                this.elements.push({id: $event.id, name: $event.name, title: $event.title, url: $event.url, type: $event.type, elements: []});
                this.$bvModal.hide('0');
            }
        },

        watch: {
            value() {
                this.elements = this.value;
            },

            elements(){
                this.$emit("input", this.elements);
            }
        }
    };
</script>
