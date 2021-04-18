<template>
  <div>
    <v-card>
      <v-card-title>
        <data-driven-data-table
            schema="BookSchema"
            :search="search"
        />
      </v-card-title>
    </v-card>
  </div>
</template>

<script>
import DataDrivenDataTable from "../common/DataDrivenDataTable";

export default {
  name: "Dashboard",
  components: {DataDrivenDataTable},

  data() {
    return {
      search: ""
    };
  }
}
</script>

<style scoped>

</style>