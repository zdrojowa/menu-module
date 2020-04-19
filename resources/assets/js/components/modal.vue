<template>
    <b-modal :id="id" :title="modalTitle" hide-footer>

        <b-input-group prepend="Nazwa" class="mt-3">
            <b-form-input v-model="name"></b-form-input>
        </b-input-group>

        <b-input-group prepend="Url" class="mt-3">
            <b-form-input v-model="url"></b-form-input>
        </b-input-group>

        <b-row>
            <b-col lg="4" class="pb-2"></b-col>
            <b-col lg="4" class="py-2">
                <b-button type="button" variant="primary" @click="save">{{ modalButton }}</b-button>
            </b-col>
        </b-row>
    </b-modal>
</template>

<script>
    export default {
        name: "modal",
        props: {
            item: {
                required: false,
                type: Object,
                default: {id: 0, name: '', url: '', elements: []}
            },
            id: {
                required: false,
                type: String,
                default: ''
            }
        },

        data() {
            return {
                isNew: true,
                name: '',
                url: ''
            };
        },

        created() {
            this.isNew = this.item.id === 0 && this.item.name === '' && this.item.url === '';
            this.name  = this.item.name;
            this.url   = this.item.url;
        },

        computed: {
            modalTitle() {
                return this.isNew? 'Dodawanie' : 'Edytowanie';
            },

            modalButton() {
                return this.isNew ? 'Dodaj' : 'Zapisz';
            }
        },
        methods: {
            save() {
                this.$emit("save", {name: this.name, url: this.url});

                this.name = '';
                this.url  = '';
            }
        }
    };
</script>
