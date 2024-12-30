import "./bootstrap";

document.addEventListener("alpine:init", () => {
    Alpine.store("darkMode", {
        on: Alpine.$persist(true).as("darkModeSetting"),
    });

    Alpine.store("gridMode", {
        on: Alpine.$persist(false).as("gridModeSetting"),
    });
});
