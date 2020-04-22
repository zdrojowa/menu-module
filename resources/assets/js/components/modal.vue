<template>
    <b-modal :id="id" :title="modalTitle" hide-footer>

        <b-form-group label="Typ" class="mt-3">
            <multiselect v-model.lazy="type" :options="types" track-by="id" label="id" placeholder="Wybierz typ"></multiselect>
        </b-form-group>

        <b-input-group v-if="isLink" prepend="Nazwa" class="mt-3">
            <b-form-input v-model="name"></b-form-input>
        </b-input-group>

        <b-input-group v-if="isLink" prepend="Url" class="mt-3">
            <b-form-input v-model="url"></b-form-input>
        </b-input-group>

        <b-form-group v-if="!isLink" label="Wybierz stronę" class="mt-3">
            <multiselect v-model.lazy="page" track-by="id" label="name" placeholder="Zaczni pisać" :options="pages" :searchable="true" @search-change="getPages">
                <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>{{ option.name }}</span><span class="custom__remove" @click="remove(option)">❌</span></span></template>
            </multiselect>
        </b-form-group>

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
                default: {id: 0, name: '', url: '', type: 'page', elements: []}
            },
            id: {
                required: false,
                type: String,
                default: ''
            },
            types: {
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
                isNew: true,
                name: '',
                url: '',
                type: {id: 'page', name: 'PAGE'},
                page: null,
                pages: []
            };
        },

        created() {
            this.isNew = this.item.id === 0 && this.item.name === '' && this.item.url === '';
            this.name  = this.item.name;
            this.url   = this.item.url;

            this.setType();
        },

        computed: {
            modalTitle() {
                return this.isNew? 'Dodawanie' : 'Edytowanie';
            },

            modalButton() {
                return this.isNew ? 'Dodaj' : 'Zapisz';
            },

            isLink() {
                return this.type.id === 'link';
            }
        },
        methods: {

            getItem(arr, key, val) {

                let item = val;

                arr.forEach(it => {
                    if (it[key] === val) {
                        item = it;
                    }
                });

                return item;
            },

            save() {

                let page_id = 0;

                if (this.type.id === 'page') {
                    this.name = this.page.name;
                    this.url  = this.page.permalink;
                    page_id   = this.page.id;
                }

                this.$emit("save", {type: this.type.id, id: page_id, name: this.name, url: this.url});

                this.type = {id: 'page', name: 'PAGE'};
                this.page = null;
                this.name = '';
                this.url  = '';
            },

            getPage: function(id) {
                axios.get('/dashboard/pages/get?id=' + id)
                    .then(res => {
                        this.page = res.data;
                    }).catch(err => {
                    console.log(err)
                })
            },

            getPages: function(query) {
                axios.get('/dashboard/pages/get?query=' + query + '&lang=' + this.lang)
                    .then(res => {
                        this.pages = [];

                        res.data.forEach(item => {
                            this.pages.push(item);
                        });
                    }).catch(err => {
                    console.log(err)
                })
            },

            setType() {
                this.type = this.getItem(this.types, 'id', this.item.type);

                if (this.type.id === 'page') {
                    this.getPage(this.item.id);
                }
            }
        },

        watch: {
            types() {
                this.setType();
            }
        }
    };
</script>
