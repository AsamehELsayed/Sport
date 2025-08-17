<script setup lang="ts">
import Button from "@/components/ui/Button.vue"
import Input from "@/components/ui/Input.vue"
import Separator from "@/components/ui/Separator.vue"

// lucide icons
import {
  Zap,
  Facebook,
  Twitter,
  Instagram,
  Youtube,
  MapPin,
  Phone,
  Mail
} from "lucide-vue-next"
</script>

<template>
  <footer class="bg-card border-t border-border">
    <div class="container mx-auto px-4 py-16">
      <!-- Newsletter Section -->
      <div class="text-center mb-16">
        <h3 class="text-3xl font-bold mb-4">
          <span class="text-foreground">Stay In </span>
          <span class="text-primary">The Game</span>
        </h3>
        <p class="text-muted-foreground mb-6 max-w-md mx-auto">
          Get exclusive access to new products, athlete stories, and special offers.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
          <Input
            placeholder="Enter your email"
            class="flex-1 bg-background border-border"
          />
          <Button class="bg-primary text-primary-foreground hover:bg-primary/90">
            Subscribe
          </Button>
        </div>
      </div>

      <Separator class="mb-16" />

      <!-- Main Footer Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-12">
        <!-- Brand -->
        <div class="lg:col-span-2">
          <div class="flex items-center space-x-2 mb-6">
            <div class="w-8 h-8 bg-gradient-primary rounded-lg flex items-center justify-center">
              <Zap class="w-5 h-5 text-primary-foreground" />
            </div>
            <span class="text-xl font-bold text-foreground">NitroSports</span>
          </div>
          <p class="text-muted-foreground mb-6 max-w-sm">
            Empowering athletes worldwide with premium sports equipment and gear. Your performance is our passion.
          </p>
          <div class="flex space-x-4">
            <Button variant="ghost" size="icon" class="hover:text-primary">
              <Facebook class="w-5 h-5" />
            </Button>
            <Button variant="ghost" size="icon" class="hover:text-primary">
              <Twitter class="w-5 h-5" />
            </Button>
            <Button variant="ghost" size="icon" class="hover:text-primary">
              <Instagram class="w-5 h-5" />
            </Button>
            <Button variant="ghost" size="icon" class="hover:text-primary">
              <Youtube class="w-5 h-5" />
            </Button>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="font-semibold text-foreground mb-4">Shop</h4>
          <ul class="space-y-3">
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Running</a></li>
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Basketball</a></li>
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Soccer</a></li>
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Tennis</a></li>
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Fitness</a></li>
          </ul>
        </div>

        <!-- Support -->
        <div>
          <h4 class="font-semibold text-foreground mb-4">Support</h4>
          <ul class="space-y-3">
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Help Center</a></li>
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Size Guide</a></li>
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Returns</a></li>
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Shipping</a></li>
            <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Contact Us</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h4 class="font-semibold text-foreground mb-4">Contact</h4>
          <div class="space-y-3">
            <div class="flex items-center space-x-3 text-muted-foreground">
              <MapPin class="w-4 h-4 text-primary" />
              <span class="text-sm">123 Sports Ave, Athletic City</span>
            </div>
            <div class="flex items-center space-x-3 text-muted-foreground">
              <Phone class="w-4 h-4 text-primary" />
              <span class="text-sm">+1 (555) 123-4567</span>
            </div>
            <div class="flex items-center space-x-3 text-muted-foreground">
              <Mail class="w-4 h-4 text-primary" />
              <span class="text-sm">support@nitrosports.com</span>
            </div>
          </div>
        </div>
      </div>

      <Separator class="mb-8" />

      <!-- Bottom Footer -->
      <div class="flex flex-col md:flex-row justify-between items-center">
        <p class="text-muted-foreground text-sm mb-4 md:mb-0">
          © 2024 NitroSports. All rights reserved.
        </p>
        <div class="flex space-x-6 text-sm">
          <a href="#" class="text-muted-foreground hover:text-primary transition-colors">Privacy Policy</a>
          <a href="#" class="text-muted-foreground hover:text-primary transition-colors">Terms of Service</a>
          <a href="#" class="text-muted-foreground hover:text-primary transition-colors">Cookies</a>
        </div>
      </div>
    </div>
  </footer>
</template>
