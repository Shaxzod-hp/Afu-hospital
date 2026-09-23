<template>
  <div class="home-page overflow-x-hidden">
    <HeroSlider />
    <ServicesSection />
    <AboutSection />
    <DoctorsCarousel />
    <NewsSection />
  </div>
</template>

<script setup>
import HeroSlider from "./HeroSlider.vue";
import ServicesSection from "./ServicesSection.vue";
import AboutSection from "./AboutSection.vue";
import DoctorsCarousel from "./Doctors.vue";
import NewsSection from "./NewsSection.vue";
</script>

<style scoped>
.home-page {
  background-color: var(--clr-bg);
}
</style>
