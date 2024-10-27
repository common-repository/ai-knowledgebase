(function ($) {
  "use strict";
  let isFirstMessage = true;
  let threadId;

  const aiKnowledgeBaseTypewriterAnimationHandler = (content, resultElement, resultsWrapper, skipButton, isBubbleChat) => {
    let textElement;
    if (isBubbleChat) {
      textElement = resultElement.querySelector("span");
    } else {
      textElement = resultElement.querySelector("h4");
    }
    let counter = 0;

    textElement.innerHTML = null;

    let interval = setInterval(() => {
      textElement.innerHTML = content.substring(0, counter);
      counter++;
      if (isBubbleChat) {
        resultsWrapper.scrollTop = resultsWrapper.scrollHeight;
      }
      if (counter === content.length + 1) {
        clearInterval(interval);
        if (skipButton !== null) {
          resultElement.removeChild(skipButton);
        }
      }
    }, 30);

    if (skipButton !== null) {
      $("#aiknowledgebase_chat_session_result_skip").on("click", () => {
        clearInterval(interval);
        textElement.innerHTML = content;
        resultElement.removeChild(skipButton);
      });
    }
  };

  const aiKnowledgeBaseTypingAnimationHandler = () => {
    const typingIndicator = document.createElement("div");
    typingIndicator.classList.add("chat_session_result_typing_indicator");
    const typingCircle = document.createElement("div");
    typingCircle.classList.add("chat_session_result_typing_circle");
    const typingShadow = document.createElement("div");
    typingShadow.classList.add("chat_session_result_typing_shadow");

    for (let i = 0; i < 3; i++) {
      const clonedCircle = typingCircle.cloneNode(true);
      typingIndicator.appendChild(clonedCircle);
    }
    for (let i = 0; i < 3; i++) {
      const clonedShadow = typingShadow.cloneNode(true);
      typingIndicator.appendChild(clonedShadow);
    }

    return typingIndicator;
  };

  const aiKnowledgeBaseGenerateRequestElement = (requestText, isBubbleChat) => {
    const requestElement = document.createElement("div");
    let requestTextElement;
    if (isBubbleChat) {
      requestElement.id = "aiknowledgebase_bubble_chat_window_content_request";
      requestTextElement = document.createElement("span");
    } else {
      requestElement.id = "aiknowledgebase_chat_session_request";
      requestTextElement = document.createElement("h4");
    }

    requestTextElement.innerHTML = requestText;

    requestElement.appendChild(requestTextElement);

    const requestIcon = document.createElement("i");

    if (!isBubbleChat) {
      requestIcon.id = "aiknowledgebase_chat_request_icon";
    }

    requestElement.appendChild(requestIcon);

    return requestElement;
  };

  const aiKnowledgeBaseGenerateResultElement = (typingAnimation, skipButton, isBubbleChat) => {
    const resultElement = document.createElement("div");
    if (isBubbleChat) {
      resultElement.id = "aiknowledgebase_bubble_chat_window_content_response";
    } else {
      resultElement.id = "aiknowledgebase_chat_session_result";
    }

    const resultIcon = document.createElement("i");
    if (!isBubbleChat) {
      resultIcon.id = "aiknowledgebase_chat_response_icon";
    }

    resultElement.appendChild(resultIcon);

    let resultTextElement;
    if (isBubbleChat) {
      resultTextElement = document.createElement("span");
    } else {
      resultTextElement = document.createElement("h4");
    }

    if (typingAnimation !== null && typingAnimation instanceof Element) {
      resultElement.appendChild(typingAnimation);
    } else {
      resultElement.appendChild(resultTextElement);
      if (skipButton !== null) {
        resultElement.appendChild(skipButton);
      }
    }

    return resultElement;
  };

  const aiKnowledgeBaseSanitizeHTML = (str) => {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  };

  $("#aiknowledgebase_chat_session_form").on("submit", async (e) => {
    e.preventDefault();

    const chatSessionContentInputValue = aiKnowledgeBaseSanitizeHTML(document.getElementById("aiknowledgebase_chat_session_content").value);
    if (!chatSessionContentInputValue) {
      alert("Missing input value");
      return;
    }

    const chatSessionMain = document.getElementById("aiknowledgebase_chat_session_main");

    const wpNonce = aiKnowledgeBaseSanitizeHTML(document.getElementById("aiknowledgebase_chat_session_form").dataset.nonce);
    const restLocation = aiKnowledgeBaseSanitizeHTML(document.getElementById("aiknowledgebase_chat_session_form").dataset.restLocation);
    const chatMessageNonce = aiKnowledgeBaseSanitizeHTML(chatSessionMain.querySelector("#chat_message_nonce").value);

    const typingAnimation = aiKnowledgeBaseTypingAnimationHandler();

    let chatSessionResultWrapper;
    if (chatSessionMain.querySelector("#aiknowledgebase_chat_session_result_wrapper") === null) {
      chatSessionResultWrapper = document.createElement("div");
      chatSessionResultWrapper.id = "aiknowledgebase_chat_session_result_wrapper";
      chatSessionMain.appendChild(chatSessionResultWrapper);
    } else {
      chatSessionResultWrapper = document.getElementById("aiknowledgebase_chat_session_result_wrapper");
    }

    const requestElement = aiKnowledgeBaseGenerateRequestElement(chatSessionContentInputValue, false);
    chatSessionResultWrapper.appendChild(requestElement);

    let resultElement = aiKnowledgeBaseGenerateResultElement(typingAnimation, null, false);
    chatSessionResultWrapper.appendChild(resultElement);

    const chatSessionResultSkip = document.createElement("a");
    chatSessionResultSkip.id = "aiknowledgebase_chat_session_result_skip";
    chatSessionResultSkip.innerHTML = "Skip &rarr;";

    const formData = new FormData();
    formData.append("chat_session_content", chatSessionContentInputValue);
    formData.append("chat_message_nonce", chatMessageNonce);
    formData.append("is_first_message", isFirstMessage);
    if (!isFirstMessage) {
      formData.append("thread_id", threadId);
    }

    document.getElementById("aiknowledgebase_chat_session_content").value = "";

    isFirstMessage = false;

    const response = await fetch(restLocation + "ai-knowledgebase/send-message", {
      method: "POST",
      headers: {
        "X-WP-Nonce": wpNonce,
      },
      body: formData,
    })
      .then(async (response) => response.json())
      .then((data) => {
        threadId = data.thread_id;
        let chatSessionResult;
        const chatSessionResultElements = document.querySelectorAll("#aiknowledgebase_chat_session_result");
        if (chatSessionResultElements.length > 0) {
          chatSessionResult = chatSessionResultElements[chatSessionResultElements.length - 1];
        }
        chatSessionResultWrapper.removeChild(chatSessionResult);
        resultElement = aiKnowledgeBaseGenerateResultElement(null, chatSessionResultSkip, false);
        chatSessionResultWrapper.appendChild(resultElement);
        aiKnowledgeBaseTypewriterAnimationHandler(data.response.content[0].text.value, resultElement, chatSessionResultWrapper, chatSessionResultSkip, false);
      })
      .catch((reason) => {
        console.error(reason);
      });
  });

  
})(jQuery);
