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
              <td v-for="(header, key) in classData.table" :key="key">
                <data-driven-data-table-cell
                    :type="header.fieldType" :value="item[header.path]"
                    :list-items="lists[header.dataSource] || []"
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
      if (this.classData && this.classData.table) {
        this.classData.table.forEach(header => {
          data.push(
              {
                text: header.name,
                value: header.path,
                _cellType: header.fieldType
              }
          )
        });
      }
      data.push(
          {
            text: '',
            value: '_actions',
            width: '80px'
          }
      )
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
    this.items = (await axios.get(this.classData.common.list, this.config)).data;
    // scan for list based headers and retrieve the lists
    for (const header of this.classData.table) {
      if (header.dataSource && !this.lists[header.dataSource || header.dataSource]) {
        if (header.dataSource) {
          this.$set(this.lists, header.dataSource, (await axios.get(header.dataSource)).data);
        } else {
          this.$set(this.lists, header.dataSource, (await axios.get(header.dataSource)).data);
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
          await axios.delete(this.classData.common.delete, {data: element, ...this.params});
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
      this.items = (await axios.get(this.classData.common.list, this.config)).data;
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