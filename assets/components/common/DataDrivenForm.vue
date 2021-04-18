<template>
  <div>
    <v-dialog
        v-model="showDialog"
        width="1400"
    >
      <v-card v-if="schema && showDialog">
        <v-card-title>
          {{ classData.common.formTitle }}
          <v-spacer/>
          <v-icon
              @click="showDialog = false"
              :disabled="isSaving"
          >mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form">
              <v-row v-for="(row, key) in classData.form" :key="key">
                <v-col class="pt-9 text-right">
                <span>
                  <span v-if="row.rules.includes('REQUIRED')" class="asterisk">*</span> {{ row.name }}
                </span>
                </v-col>
                <v-col cols="8">
                  <data-driven-input v-model="model[row.path]" :form-input-data="row"/>
                </v-col>
                <v-col/>
              </v-row>
            </v-form>
            <v-row class="text-right">
              <v-col cols="10">
                <v-spacer/>
                <v-btn
                    @click="showDialog = false"
                    class="mr-4"
                    :disabled="isSaving"
                >
                  cancel
                </v-btn>
                <v-btn
                    @click="save"
                    :loading="isSaving"
                >
                  save
                </v-btn>
              </v-col>
              <v-col/>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import DataDrivenInput from "./DataDrivenInput";
const axios = require('axios').default;

export default {
  name: "DataDrivenForm",
  components: {DataDrivenInput},
  props: {
    schema: {
      required: true,
      type: String
    },
    classData: {
      required: true,
      type: Object
    },
    config: {
      required: true,
      type: Object,
    }
  },

  data() {
    return {
      showDialog: false,
      isSaving: false,
      model: {}
    };
  },

  methods: {
    createFromData(data) {
      this.model = {};
      this.classData.form.forEach(formEntry => {
        this.$set(this.model, formEntry.path, data ? data[formEntry.path] : data);
      });
      if (data && data.id) {
        this.$set(this.model, 'id', data.id);
      }
    },

    openDialog(data) {
      this.createFromData(data);
      this.showDialog = true;
    },

    async save() {
      if (this.$refs.form.validate()) {
        this.isSaving = true;
        try {
          console.log(this.model);
          await axios.post(this.classData.common.post, this.model, this.config);
          this.$emit('saved');
          this.$emit('success');
        } catch(e) {
          console.log(e);
          this.$emit('failed');
        }
        this.isSaving = false;
      }
    }
  }
}
</script>

<style>
  .asterisk {
    color: #ff5555 !important;
  }
</style>