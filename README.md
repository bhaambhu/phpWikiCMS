
# PHP Wiki CMS

A framework that allows you to easily create a wiki-style page management system, supports tree-style relationships, adding questions to pages and tags to questions.

![App Screenshot](https://raw.githubusercontent.com/bhaambhu/phpWikiCMS/main/screenshot.png)


## Features

- Full Wiki CMS GUI.
- Simply add pages with ```[[topicName]]``` or ```[[topicName|DisplayName]]``` while writing the body section of any topic. It converts into a link while previewing the topic, and color of the link is according to whether the linked topic exists or is empty.
- Tree View.
- Maintains a trace of recently read topics for easily navigate back to them.
- Relationship between topics and questions, and questions and question-types, and there's also subjects which is another layer for grouping topics.


## Why I Made This Project
I made this project to collect knowledge on various computer science topics as I studied them. While studying one topic we often encouter new unknown terms which are separate topics that we currently don't know but might feed info upon in the future. 

I made this framework in which while arbitrarily writing what I learnt about a topic, I can specify terms which lead to other pages on the fly, pages that either currently exist, or terms which I might write upon in the future. I also needed to store questions related to topics, and questions to be tagged with question-types for further grouping and easier revisions in the future. 

This was the motivation for making this project - storing personal study notes.
## Disclaimer

I made this project in 2017 but I am rebasing the commits and pushing it to github in December 2022 (that's what the commit dates above reflect).

Now that I look back at it after years of experience, this project could be structured in a better way, but since php is kinda dead now, I didn't do any effort to restructure or improve this project, but someone could maybe find this project useful so I'm putting this on github.
## Tech

PHP, MySQL, CKEditor.
## Design

It features a minimal design that is easy to navigate. It's built on a simple grey pallette that is easy on the eyes for long time reading.
