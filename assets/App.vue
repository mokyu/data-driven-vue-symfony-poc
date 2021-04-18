<template>
  <v-app>
    <v-card
        color="grey lighten-4"
        flat
        height="100%"
        tile
    >
      <v-toolbar dense>
        <v-app-bar-nav-icon/>
        <v-toolbar-title>De Boekenclub</v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col>
              <router-view/>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-app>
</template>

<script>
export default {
  name: "App"
}
</script>

<style scoped>

</style>