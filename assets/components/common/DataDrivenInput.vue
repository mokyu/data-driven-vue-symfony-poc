<template>
  <div>
    <template v-if="formInputData.fieldType === 'TextField'">
      <v-text-field
        key="textfield"
        :value="value"
        @input="data => $emit('input', data)"
        :rules="getRules(formInputData)"
        :placeholder="formInputData.placeholder"
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'MemberField'">
      <v-autocomplete
          :value="value"
          :loading="isLoadingItems"
          @input="data => $emit('input', data)"
          :rules="getRules(formInputData)"
          :items="items"
          item-text="name"
          item-value="id"
          :placeholder="formInputData.placeholder"
          clearable
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'EnumField'">
      <v-autocomplete
          :value="value"
          :loading="isLoadingItems"
          @input="data => $emit('input', data)"
          :rules="getRules(formInputData)"
          :items="items"
          item-text="value"
          item-value="key"
          :placeholder="formInputData.placeholder"
          clearable
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'TextArea'">
      <v-textarea
          :value="value"
          @input="data => $emit('input', data)"
          :rules="getRules(formInputData)"
          :placeholder="formInputData.placeholder"
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'DateField'">
      <v-menu offset-y :close-on-content-click="false">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
              color="primary"
              dark
              v-bind="attrs"
              v-on="on"
          >
            {{ value || 'Selecteer een datum'}}
          </v-btn>
        </template>
        <v-card>
          <v-date-picker
              :value="value"
              @input="data => $emit('input', data)"
              :rules="getRules(formInputData)"
              :placeholder="formInputData.placeholder"
          />
        </v-card>
      </v-menu>
    </template>

    <template v-else>
      Unknown fieldType: {{formInputData.fieldType}}
    </template>
  </div>
</template>

<script>
import axios from "axios";
const moment = require('moment');
import {LIMIT_LENGTH, REQUIRED} from "./rules";
export default {
name: "DataDrivenInput",
  props: {
    value: {
      required: true
    },
    formInputData: {
      required: true
    }
  },

  data() {
    return {
      items: [],
      isLoadingItems: false,
      futureDateFields: {
        days: 0,
        hours: 0,
        minutes: 0
      },
    };
  },

  methods: {
    getRules() {
      let rules = [];
      this.formInputData.rules.forEach(rule => {
        const params = rule.split(';');
        switch(params[0]) {
          case 'REQUIRED':
            rules.push(REQUIRED());
            break;
          case 'LIMIT_LENGTH':
            rules.push(LIMIT_LENGTH(params[1]));
            break;
        }
      })
      return rules;
    },
  },

  async mounted() {
    this.isLoadingItems = true;
    if (this.formInputData.dataSource) {
      this.items = (await axios.get(this.formInputData.dataSource)).data;
    }
    this.isLoadingItems = false;
  }
}
</script>

<style scoped>

</style>