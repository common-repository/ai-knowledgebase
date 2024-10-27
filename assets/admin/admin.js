(function ($) {
  "use strict";
  // sticky header/menu.
  $(window).on("scroll", function () {
    if ($(window).scrollTop() > 60) {
      $(".aiknowledgebase-admin-wrap").addClass("aiknowledgebase-stick-settings");
    } else {
      if ($(".aiknowledgebase-admin-wrap").hasClass("aiknowledgebase-stick-settings")) {
        $(".aiknowledgebase-admin-wrap").removeClass("aiknowledgebase-stick-settings");
      }
    }
  });

  const trainingTypeSelect = document.getElementById("assistant_training_type");
  const assistantFileWrapper = document.getElementById("assistant_file_wrapper");
  if (trainingTypeSelect && assistantFileWrapper) {
    const assistantFileWrapperParent = assistantFileWrapper.parentNode;
    assistantFileWrapperParent.removeChild(assistantFileWrapper);

    trainingTypeSelect.addEventListener("change", () => {
      if (trainingTypeSelect.value === "file_upload") {
        assistantFileWrapperParent.appendChild(assistantFileWrapper);
      } else {
        const childElement = document.getElementById("assistant_file_wrapper");
        if (childElement !== null && childElement !== undefined) {
          assistantFileWrapperParent.removeChild(assistantFileWrapper);
        }
      }
    });
  }

  const aiKnowledgeBaseCreateLoaderElement = (id) => {
    const loader = document.createElement("div");
    loader.id = id;
    loader.classList.add("assistant_loader");

    return loader;
  };

  $("#assistant_file_form").on("submit", async (e) => {
    e.preventDefault();

    const submitAssistantForm = document.getElementById("assistant_file_form");
    const submitAssistantButton = document.getElementById("submit_assistant_settings");
    const submitAssistantTrainingType = document.getElementById("assistant_training_type");

    const submitAssistantLoader = aiKnowledgeBaseCreateLoaderElement("submit_assistant_loader");

    submitAssistantForm.removeChild(submitAssistantButton);
    submitAssistantForm.appendChild(submitAssistantLoader);

    const wpNonce = document.getElementById("assistant_file_form").dataset.nonce;
    const restLocation = document.getElementById("assistant_file_form").dataset.restLocation;
    const assistantSettingsNonce = document.getElementById("assistant_settings_nonce").value;

    const formData = new FormData();

    if (submitAssistantTrainingType.value === "file_upload") {
      const fileInput = document.getElementById("assistant_file");
      const assistantFile = fileInput.files[0];
      formData.append("assistant_file", assistantFile);
    }

    const modelInput = document.getElementById("assistant_model");
    const assistantModel = modelInput.value;

    formData.append("assistant_model", assistantModel);
    formData.append("assistant_training_type", submitAssistantTrainingType.value);
    formData.append("assistant_settings_nonce", assistantSettingsNonce);

    const response = await fetch(restLocation + "ai-knowledgebase/generate-assistant", {
      method: "POST",
      headers: {
        "X-WP-Nonce": wpNonce,
      },
      body: formData,
    })
      .then(async (response) => response.json())
      .then((data) => {
        submitAssistantLoader.classList.remove("assistant_loader");
        submitAssistantLoader.innerHTML = data.response;
        setTimeout(() => {
          location.reload();
        }, 1500);
      })
      .catch((reason) => {
        console.error(reason);
      });
  });

  $("#reset_assistant_form").on("submit", async (e) => {
    e.preventDefault();

    const resetAssistantForm = document.getElementById("reset_assistant_form");
    const resetAssistantButton = document.getElementById("reset_assistant");

    const resetAssistantLoader = aiKnowledgeBaseCreateLoaderElement("reset_assistant_loader");

    resetAssistantForm.removeChild(resetAssistantButton);
    resetAssistantForm.appendChild(resetAssistantLoader);

    const wpNonce = document.getElementById("reset_assistant_form").dataset.nonce;
    const restLocation = document.getElementById("reset_assistant_form").dataset.restLocation;

    await fetch(restLocation + "ai-knowledgebase/reset-assistant", {
      method: "POST",
      headers: {
        "X-WP-Nonce": wpNonce,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        resetAssistantLoader.classList.remove("assistant_loader");
        resetAssistantLoader.innerHTML = data.response;
        setTimeout(() => {
          location.reload();
        }, 1500);
      })
      .catch((reason) => {
        console.error(reason);
      });
  });

  $("#assistant_customize_form").on("submit", async (e) => {
    e.preventDefault();

    const assistantCustomizeForm = document.getElementById("assistant_customize_form");
    const submitAssistantCustomizeButton = document.getElementById("submit_assistant_customize");

    const customizeAssistantLoader = aiKnowledgeBaseCreateLoaderElement("assistant_customize_loader");

    assistantCustomizeForm.removeChild(submitAssistantCustomizeButton);
    assistantCustomizeForm.appendChild(customizeAssistantLoader);

    const wpNonce = assistantCustomizeForm.dataset.nonce;
    const restLocation = assistantCustomizeForm.dataset.restLocation;
    const customizeAssistantNonce = document.getElementById("customize_assistant_nonce").value;

    const formData = new FormData();

    const shortcodeInputPlaceholderInput = document.getElementById("assistant_shortcode_input_placeholder");
    const shortcodeSubmitButtonInput = document.getElementById("assistant_shortcode_submit_button");

    formData.append("shortcode_input_placeholder", shortcodeInputPlaceholderInput.value);
    formData.append("shortcode_submit_button", shortcodeSubmitButtonInput.value);
    formData.append("customize_assistant_nonce", customizeAssistantNonce);

    

    await fetch(restLocation + "ai-knowledgebase/customize-assistant", {
      method: "POST",
      headers: {
        "X-WP-Nonce": wpNonce,
      },
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data === false) {
          assistantCustomizeForm.removeChild(customizeAssistantLoader);
          assistantCustomizeForm.appendChild(submitAssistantCustomizeButton);
        }
        if (data.success) {
          customizeAssistantLoader.classList.remove("assistant_loader");
          customizeAssistantLoader.innerHTML = data.response;
          setTimeout(() => {
            location.reload();
          }, 1500);
        }
      })
      .catch((reason) => {
        console.error(reason);
      });
  });

  $(".customize-assistant-input").on("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
    }
  });
})(jQuery);
