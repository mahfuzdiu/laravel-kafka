package com.example.spring_kafka_consumer.controllers;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class ExampleController {
    @GetMapping("/")
    public String welcome()
    {
        return "Welcome to spring boot";
    }
}
