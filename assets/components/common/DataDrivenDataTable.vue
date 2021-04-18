<template>
    <v-card>
      <v-card-title>
        <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            label="Search"
            single-line
            hide-details
        />
        <v-btn
            v-if="expandable"
            class="mt-3 ml-4"
            @click="items.forEach(item => item._expanded = true)"
        >
          Expand All
        </v-btn>
        <v-btn
            class="mt-3 ml-4"
            @click="$refs.form.openDialog(null)"
        >
          create
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-data-table
            v-bind="$props"
            :headers="generatedHeaders"
            :items="items"
            :search="search"
            v-if="!isLoading"
            :options="options"
        >
          <template v-slot:item="{ item }">
            <tr>
              <td v-if="expandable">
                <v-icon @click="item._expanded = !item._expanded">
                  {{ item._expanded ? 'mdi-chevron-up' : 'mdi-chevron-down' }}
                </v-icon>
              </td>
              <td v-for="(header, key) in classData.header" :key="key">
                <data-driven-data-table-cell
                    :type="header.type" :value="item[header.path]"
                    :list-items="lists[header.listFromEnum || header.listFromSchema] || []"
                    :header="header"
                />
              </td>
              <td>
                <v-icon
                    small
                    class="mr-2"
                    @click="$refs.form.openDialog(item)"
                    :disabled="isDeleting"
                >
                  mdi-pencil
                </v-icon>
                <v-icon
                    small
                    v-if="!isDeleting"
                    @click="deleteElement(item)"
                >
                  mdi-delete
                </v-icon>
                <v-progress-circular
                    v-else-if="isDeleting"
                    :size="16"
                    :width="2"
                    indeterminate
                />
              </td>
            </tr>
            <tr v-if="expandable && item._expanded">
                <td :colspan="generatedHeaders.length" style="border: none !important; background-color: #FFF !important;" class="pa-4">
                  <slot name="expanded_item" v-bind:item="item">
                    content
                  </slot>
                </td>
            </tr>
          </template>
        </v-data-table>
        <v-container v-else fluid>
          <v-row justify="center" align="center">
            <v-progress-circular indeterminate/>
          </v-row>
        </v-container>
      </v-card-text>
      <data-driven-form
          :config="config"
          v-if="!isLoading"
          :class-data="classData"
          :schema="schema"
          ref="form"
          @saved="reloadData"
          @failed="failSave"
          @success="successSave"
      />
      <v-snackbar v-model="bar">
        {{ barText }}
      </v-snackbar>
    </v-card>
</template>

<script>
import DataDrivenForm from "./DataDrivenForm";
import DataDrivenDataTableCell from "./DataDrivenDataTableCell";
const axios = require('axios').default;

export default {
name: "DataDrivenDataTable",
  components: {DataDrivenDataTableCell, DataDrivenForm},
  props: {
    schema: {
      required: true,
      type: String
    },
    deleteMessage: {
      required: false,
      type: String,
      default: 'Are you sure you want to delete this item?<br/>Once deleted it\'s gone forever.'
    },
    expandable: {
      required: false,
      type: Boolean,
      default: false
    },
    forceNoCreate: {
      required: false,
      type: Boolean,
      default: false
    },
    params: {
      required: false,
      type: Object,
      default: () => {return {}; }
    }
  },

  data() {
    return {
      search: '',
      expanded: [],
      classData: null,
      items: [],
      lists: {},
      isLoading: true,
      isDeleting: false,
      options: {
        page: 1,
        itemsPerPage: 10,
        sortBy: [],
        sortDesc: [],
        groupBy: [],
        groupDesc: [],
        multiSort: false,
        mustSort: false
      },
      bar: false,
      barText: ''
    };
  },

  computed: {
    generatedHeaders() {
      let data = [];
      if (this.classData && this.classData.header) {
        this.classData.header.forEach(header => {
          data.push(
              {
                text: header.name,
                value: header.path,
                _cellType: header.type
              }
          )
        });
      }
      if (this.expandable) {
        data.unshift({
          text: '',
          value: '_expand',
          width: '40px'
        })
      }
      if (this.classData && this.classData.general && (this.classData.general.updatable || this.classData.general.deletable)) {
        data.push(
            {
              text: '',
              value: '_actions',
              width: '80px'
            }
        )
      }
      return data;
    },

    config() {
      return {
        params: this.params
      };
    }
  },

  async mounted() {
    this.classData = (await axios.get(`/api/metadata/${this.schema}`)).data;
    this.items = (await axios.get(`/api/data/${this.schema}/list`, this.config)).data;
    if (this.expandable) {
      this.items.forEach(item => {
        this.$set(item, '_expanded', false);
        this.$set(item, '_search', null);
      })
    }
    // scan for list based headers and retrieve the lists
    for (const header of this.classData.header) {
      if (header.type === 'List' && !this.lists[header.listFromEnum || header.listFromSchema]) {
        if (header.listFromEnum) {
          this.$set(this.lists, header.listFromEnum, (await axios.get(`/api/enum/${header.listFromEnum}/list`)).data);
        } else {
          this.$set(this.lists, header.listFromSchema, (await axios.get(`/api/data/${header.listFromSchema}/list`)).data);
        }
      }
      if (header.sort) {
        this.options.sortBy.push(header.path);
        this.options.sortDesc.push(header.sort === 'DESC');
      }
    }
    this.isLoading = false;
  },

  methods: {
    async deleteElement(element) {
      if (await this.$confirm(this.deleteMessage, { color: "warning", title: "Warning" })) {
        this.isDeleting = true;
        try {
          await axios.post(`/api/data/${this.schema}/delete`, element, this.config);
        } catch(e) {
          this.bar = true;
          this.barText = 'Something went wrong while deleting.';
          this.isDeleting = false;
          return;
        }
        this.bar = true;
        this.barText = 'Item successfully deleted.';
        this.isDeleting = false;
        await this.reloadData();
      }
    },

    async reloadData() {
      this.isLoading = true;
      this.items = (await axios.get(`/api/data/${this.schema}/list`, this.config)).data;
      if (this.expandable) {
        this.items.forEach(item => {
          this.$set(item, '_expanded', false);
          this.$set(item, '_search', null);
        })
      }
      this.isLoading = false;
    },

    failSave() {
      this.bar = true;
      this.barText = 'Something went wrong while saving.';
    },

    successSave() {
      this.bar = true;
      this.barText = 'Item successfully saved.';
    }
  }
}
</script>